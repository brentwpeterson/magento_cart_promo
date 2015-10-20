<?php

/**
 * 
 * Need to update insert Coupon for 1.4
 * **/

class Wdc_Cartex_Model_Rules extends Wdc_Cartex_Model_Discount
{
	protected $_verId;
	protected $_couponQty;
	protected $_couponLen;
	protected $_use;
	protected $_custuse;
	protected $_discount;
	protected $_prefix;
	protected $_cartexId;
	protected $_couponId;
	
	public function __construct()
	{
		$this->_verId = $this->checkVersion();	
	}
	
	public function getRuleModel()
	{
		if($this->checkVersion() == 2){
		
			return Mage::getModel('salesrule/coupon');
		}
		else{
			return Mage::getModel('salesrule/rule');	
		}	
	}
		
	public function getVersion()
	{
		$this->_verId = $this->checkVersion();	
		return $this->_verId;
	}
	
	public function getCouponCode($ruleId)
	{
		$model = $this->getRuleModel()->load($ruleId);
		if($this->checkVersion() == 2){
			
			return $model->getCode();
		}
		else{
			return  $model->getCouponCode();	
		}
	}
		
	public function createCouponCode() {
		
		if(strlen($this->_prefix) >= 1){
			$code = $this->_prefix;
		}
		else{
			$code = '';
		}
		
		if($this->_couponLen > 1){
			$j = $this->_couponLen;
		}
		else{
			$j = 2;
		}
		
		$chars = "ABCDEFGHIJKLMNPQRSTUVWXYZ123456789";
		srand((double)microtime()*1000000);
		$i = 0;
		
		while ($i <= $j)
		{
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$code = $code . $tmp;
			$i++;
		}
	return $code;	
	}
	
	public function couponGenerator($cartexId, $conum, $colen, $prex='', $use=0, $discount=0, $custuse=0)
	{
		$prexlen = strlen($prex);
		
		$this->_couponLen = $colen - $prexlen;
		$this->_prefix = $prex;
		$this->_cartexId = $cartexId;
		$this->_use = $use;
		$this->_custuse = $custuse;
		$this->_discount = $discount;
		
		if(is_numeric($conum) && $conum > 0){
			foreach ($this->createCouponCollection($conum) as $coupon){
				$this->insertCoupon($coupon);
			}
		}		
	}
		
	public function couponDuplicator($couponId, $conum, $colen, $prex='')
	{
		$prexlen = strlen($prex);		
		$this->_couponLen = $colen - $prexlen;
		$this->_prefix = $prex;
		$this->_couponId = $couponId;
		
		if(is_numeric($conum) && $conum > 0){
			foreach ($this->createCouponCollection($conum) as $coupon){
				try{
				$this->duplicateCoupon($coupon);
			}
			catch(exception $e)
			{
				Mage::helper('errorlog')->insert("coupDup", $e->getMessage());
			}
			}
		}		
	}
	
	public function createCouponCollection($j=1)
	{
		$coupons = array();
		$i=0;
		while ($i <	$j)
		{
			$code = $this->createCouponCode();	
			if(!in_array($code, $coupons))
			{
				$coupons[] = $code;		
				$i++;
			}			
		}		
		return $coupons;
	}
	

	public function insertCoupon($coupon)
	{				
		$model = $this->getRuleModel();
		$promoModel = Mage::getModel('cartex/cart_entity')->load($this->_cartexId);
		try{			
			if($this->checkVersion() == 2){				
				$rule = Mage::getModel('salesrule/rule')
					->setName($promoModel->getPromoName().' > '.$coupon)
					->setDescription($promoModel->getPromoDescription())
					->setFromDate(date('Y-m-d_H-i-s'))				
					->setCustomerGroupIds($this->getCustomerGroups())
					->setIsActive(1)				
					//->setStopRulesProcessing 
					//->setIsAdvanced 					
					->setSimpleAction('cart_fixed') 
					//->setDiscountAmount 
					//->setDiscountQty 
					//->setDiscountStep 
					->setUsesPerCustomer($this->_custuse)						
					->setStopRulesProcessing(0)
					->setIsRss(1)
					->setWebsiteIds(1)
					->setCouponType(2)
					->save();	
					
				$model->setRuleId($rule->getId())
					->setCode($coupon)
					->setIsPrimary(1)
					->setUsageLimit($this->_use)
					->setUsagePerCustomer($this->_custuse)
					->save();
					
				$couponModel = Mage::getModel('cartex/cart_coupon')
					->setCouponCode($coupon)
					->setDiscountAmount($this->_discount)
					->setRuleId($model->getRuleId())
					->setCartexId($this->_cartexId)
					->setUseRules(0)
					->setIsCurrent(1)
					->save();
			}
			else{			
				$model
					->setName($promoModel->getPromoName().' > '.$coupon)
					->setDescription($promoModel->getPromoDescription())
					->setFromDate(date('Y-m-d_H-i-s'))				
					->setCouponCode($coupon)
					->setUsesPerCoupon($this->_use)
					->setUsesPerCustomer($this->_custuse)		
					->setCustomerGroupIds($this->getCustomerGroups())
					->setIsActive(1)				
					->setSimpleAction('cart_fixed') 					
					->setWebsiteIds(1)
					->save();
				
				$couponModel = Mage::getModel('cartex/cart_coupon')
					->setCouponCode($coupon)
					->setDiscountAmount($this->_discount)
					->setRuleId($model->getRuleId())
					->setCartexId($this->_cartexId)
					->setUseRules(0)
					->setIsCurrent(1)
					->save();
			}
			}
			catch(exception $e)
		{
			return $e->getMessage();	
		}
		
		
		
		
	}
	
	public function copyCoupon($coupon)
	{
		////Mage::helper('errorlog')->insert('couponDup', $this->_couponLen.'-'.$this->_prefix.'-'.$coupon.' - '.$this->_couponId);
		$ruleModel =  Mage::getModel('salesrule/rule')
			->load($this->_couponId);	
		
		$newCoupon = Mage::getModel('salesrule/rule')->setData($ruleModel->getData())
			->setCode($coupon)
			->save();
			
		
	}
	
	public function duplicateCoupon($coupon)
	{				
		$model = $this->getRuleModel();
		$ruleModel =  Mage::getModel('salesrule/rule')->load($this->_couponId);			
		$couponModel = Mage::getModel('salesrule/coupon')->loadPrimaryByRule($ruleModel);
			
		try{
			
			if($this->checkVersion() == 2){
				
				////Mage::helper('errorlog')->insert('couponDup actionserial', $ruleModel->getActionsSerialized());
				
				$rule = Mage::getModel('salesrule/rule')
					->setName('duplicate-'.$ruleModel->getName())
					->setDescription($ruleModel->getDescription())
					->setToDate($ruleModel->getToDate()) //`salesrule`.`to_date`,
					->setFromDate($ruleModel->getFromDate())				
					->setCustomerGroupIds($ruleModel->getCustomerGroupIds()) //`salesrule`.`customer_group_ids`,
					->setIsActive($ruleModel->getIsActive())	//`salesrule`.`is_active`,			
					->setStopRulesProcessing($ruleModel->getStopRulesProcessing())
					->setIsAdvanced($ruleModel->getIsAdvanced())	//`salesrule`.`is_advanced`,				
					->setSimpleAction($ruleModel->getSimpleAction()) //`salesrule`.`simple_action`,
					->setDiscountAmount($ruleModel->getDiscountAmount()) //`salesrule`.`discount_amount`,
					->setDiscountQty($ruleModel->getDiscountQty()) //`salesrule`.`discount_qty`,
					->setDiscountStep($ruleModel->getDiscountStep()) //`salesrule`.`discount_step`,
					->setUsesPerCustomer($ruleModel->getUsesPerCustomer())	//`salesrule`.`uses_per_customer`,					
					->setStopRulesProcessing($ruleModel->getStopRulesProcessing())  //`salesrule`.`stop_rules_processing`,
					->setIsRss($ruleModel->getIsRss())  //`salesrule`.`is_rss`,
					->setWebsiteIds($ruleModel->getWebsiteIds()) //`salesrule`.`website_ids`,
					->setCouponType($ruleModel->getCouponType())  //`salesrule`.`coupon_type`	
				//	->setConditionsSerialized($ruleModel->getConditionsSerialized())//`salesrule`.`conditions_serialized`,
				//	->setActions($ruleModel->getActionsSerialized())  //`salesrule`.`actions_serialized`,
					->setProductIds($ruleModel->getProductIds()) //`salesrule`.`product_ids`,
					->setSortOrder($ruleModel->getSortOrder()) //`salesrule`.`sort_order`,
					->setSimpleFreeShipping($ruleModel->getSimpleFreeShipping()) //`salesrule`.`simple_free_shipping`,
					->setApplyToShipping($ruleModel->getApplyToShipping()) //`salesrule`.`apply_to_shipping`,
					//`salesrule`.`times_used`,
					->save();	
				
				//Mage::helper('errorlog')->insert('couponDup actionserial', $ruleModel->getId().'->'.$rule->getId());	
				Mage::getresourceModel('cartex/cart_coupon')->updateRuleActions($ruleModel->getId(), $rule->getId());
									
				$model->setRuleId($rule->getId())
					->setCode($coupon)
					->setIsPrimary($couponModel->getIsPrimary())
					->setUsageLimit($couponModel->getUsageLimit())
					->setUsagePerCustomer($couponModel->getUsagePerCustomer())
					->setExpirationDate($couponModel->getExpirationDate())
					->save();	
			}
			else{			
				$model
					->setName($promoModel->getPromoName().' > '.$coupon)
					->setDescription($promoModel->getPromoDescription())
					->setFromDate(date('Y-m-d_H-i-s'))				
					->setCouponCode($coupon)
					->setUsesPerCoupon($this->_use)
					->setUsesPerCustomer($this->_custuse)		
					->setCustomerGroupIds($this->getCustomerGroups())
					->setIsActive(1)				
					->setSimpleAction('cart_fixed') 					
					->setWebsiteIds(1)
					->save();
				}
		}
		catch(exception $e)
		{
			//Mage::helper('errorlog')->insert('error', $e->getMessage());	
		}
	}
	
	
	protected function getCustomerGroups()
	{
		$groupIds = array();
		$collection = Mage::getModel('customer/group')->getCollection();
		foreach ($collection as $customer)
		{
			$groupIds[] = $customer->getId();	
		}	
		
		return $groupIds;
	}
	
	public function updateCoupon($cartexCouponId, $ruleId, $couponCode)
	{				
		$model = $this->getRuleModel();
		
		try{
			
			if($this->checkVersion() == 2){				

				$couponId = Mage::getresourceModel('cartex/cart_coupon')->getCouponIdbyRuleId($ruleId);
															
				$model->load($couponId)
				->setCode($couponCode)->save();
				
				$couponModel = Mage::getModel('cartex/cart_coupon')
					->setId($cartexCouponId)
					->setCouponCode($couponCode)		
					->save();
			}
			else{			
				$model
					->load($ruleId)				
					->setCouponCode($couponCode)					
					->save();
				$couponModel = Mage::getModel('cartex/cart_coupon')
					->setId($cartexCouponId)
					->setCouponCode($couponCode)		
					->save();			
			}
		}
		catch(exception $e)
		{
			echo $e->getMessage();	
		}
	}
	
	
	

	
}