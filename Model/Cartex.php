<?php

/**
 *  * DO TO
 * Buy item * x get cheapest free
 * getPromoGroupCollection is the same as getProductInsertCollection() Need to combine them
 *
 */
class Wdc_Cartex_Model_Cartex extends Wdc_Cartex_Model_Cart
{		
	protected $_currentPromoCollection;
	protected $_currentItemCollection = array();
	protected $_currentGroupCollection = array();
	protected $_currentCouponCollection = array();
	protected $_currentCartProductIdCollection = array();
	protected $_currentGroupedProductCollection = array();
	protected $_couponCode;
	protected $_ruleId;
	protected $_itemLimit;
	protected $_qualifyItemCount;
	protected $_cartItemsCount;
	protected $_promoType;
	protected $_exceptType;
	protected $_productGroup = null;
	protected $_positionId;
	protected $_entityTypeId;
	protected $_xy = false;
	protected $_couponOnly = false;
	protected $_discount = false;
	protected $_discount_itemid;
	protected $_discount_amount;
	protected $_promo;
	protected $_quoteId;
	protected $_sideAlert = false;

	
	public function __construct()
	{
		$this->_cartTotal = Mage::getModel('checkout/session')->getQuote()->getsubtotal();
		$this->_quoteId = Mage::getModel('checkout/session')->getQuoteId();
		$this->_items = Mage::getresourceModel('cartex/cart_item')->getCurrentCart();
		$this->_couponCode = Mage::getModel('checkout/session')->getQuote()->getCouponCode();
		$this->_cartItemsCount = (int)Mage::getModel('checkout/session')->getQuote()->getItemsQty();
	}
	
	public function cartPrepare($set=false)
	{

	}
	
	public function setCartFunctions($positionId=1)
	{				
		$this->_positionId = $positionId;
		$cartCollection = $this->getActivePromoGroups();
		
		if($cartCollection)
		{					
			foreach ($cartCollection as $promos)
			{			
				$this->processRequests($promos);		
			}
		}	
	}
	

	
	protected function getCurrentCouponCollection()
	{		
		
		$this->_currentCouponCollection = array();
		$couponCollection = Mage::getresourceModel('cartex/cart_coupon_collection')
			->addFilter('cartex_id', $this->_promoGroupId);
		
		foreach ($couponCollection as $item)
		{
			$this->_currentCouponCollection[] = strtoupper($item->getCouponCode());	
		}	
		
		//if(!empty($this->_ruleId) && $this->_ruleId != 0){
		//	$this->_currentCouponCollection[] = strtoupper(Mage::getModel('cartex/rules')->getCouponCode($this->_ruleId));
		//}
		
		return $this->_currentCouponCollection;
	
	}
	

	
	protected function checkCurrentCoupon()
	{		
		$val = false;
		if(in_array(strtoupper($this->_couponCode), $this->getCurrentCouponCollection()))
			{			
				$val = $this->checkExistingCoupons();				
			}	
		return $val;
	}
	
	protected function checkExistingCoupons()
	{
		$val = true;
		$quoteItemCollection = Mage::getresourceModel('cartex/cart_idx_collection')
			->addFilter('quote_id', $this->_quoteId);					
	
			foreach ($quoteItemCollection as $item){
				$curPromoType = Mage::getModel('cartex/cart_entity')
					->load($item->getCartexId())
					->getPromoType();
				
				if(in_array($curPromoType, $this->setCouponTypes())){				
				
					if(!in_array($item['product_id'], $this->getProductInsertCollection())){						
											
					$this->removeItembyProductId($item['product_id'], 'checkexistingcoupon');
						
						$val = false;
					}
				}
			}			
		return $val;
	}
	
	protected function setCouponTypes()
	{
		return array(2,3,7,8);	
	}
	
	
	protected function processRequests($promos)
	{		
		$this->setCurrentPromoCollection($promos);
		
			switch($this->_promoType)
			{
				case 0:
					$this->processPriceRequests();
					break;
				case 1:				
					$this->processXforYRequests();
					break;
				case 2:				
					$this->processValueCouponCodeRequests();				
					break;
				case 3:				
					$this->processXYCouponCodeRequests();					
					break;
				case 4:				
					$this->processGroupedProducts();					
					break;
				case 5:				
					$this->buyXGetcheapestFree();					
					break;
				case 6:	
					$this->_xy = true;			
					$this->processPriceRequests();					
					break;
				case 7:	
					$this->_xy = true;				
					$this->processValueCouponCodeRequests();					
					break;
				case 8:	
					$this->processCouponCodeRequests();					
					break;
				case 9:	
					$this->_discount = true;										
					$this->processCouponCodeRequests();					
					break;
				case 10:						
					$this->_discount = true;										
					$this->processXYDiscountRequests();				
					break;
				case 11:													
					$this->_sideAlert = true;
					$this->processChooseGift();				
					break;
				case 12:	
					Mage::getModel('cartex/rules_buyxgetfree')->processRule($promos);		
					break;
				default:				
					break;				
			}
	}
	
	
	/**
	 * This is method checkProductbeforeInsert
	 *
	 * @return bool Return true/false based on weather the product is in the idx table
	 * No sure if we need this 08/06/10
	 */
	protected function checkProductbeforeInsert()
	{	
		$val = true;
		if(isset($this->_items)){	
			foreach ($this->_items as $item)
			{				
				if(in_array($item['product_id'], $this->getProductInsertCollection())){					
					if(Mage::getModel('cartex/item')->checkInsertProduct($item['product_id'])){					
						$val = false;
					}
				}	
			}
		}
		return $val;	
	}
	
	protected function buyXGetcheapestFree()
	{
		
	}
	
	protected function processXYDiscountRequests()
	{		
		$this->processXforYRequests();
	}
	
	protected function processGroupedProducts()
	{
		foreach ($this->_items as $item)
		{
			if(in_array($item['product_id'], $this->getGroupedPromoProductCollection()))
			{
				$this->_productGroup = true;
			}
		}		
	}
	
	protected function getGroupedPromoProductCollection()
	{
			$this->_currentGroupedProductCollection = array();
			$groupedproductCollection = Mage::getresourceModel('cartex/cart_products_collection')
				->addFilter('wdc_attribute_id', $this->_promoGroupId);	
			foreach ($groupedproductCollection as $item)
			{
				$this->_currentGroupedProductCollection[] = (int)$item->getEntityId();		
			}		
			return $this->_currentGroupedProductCollection;
	}
	
	protected function processXYCouponCodeRequests()
	{				
		if(isset($this->_couponCode))
		{			
			if($this->checkCurrentCoupon())
			{				
				$this->processXforYRequests();				
			}
		}
	}
	
	protected function processValueCouponCodeRequests()
	{		
		if(isset($this->_couponCode))
		{	
			if($this->checkCurrentCoupon())
			{
				$this->processPriceRequests();
			}
		}
	}
	
	protected function processCouponCodeRequests()
	{			
		$this->getProductInsertCollection();
				
		if(isset($this->_couponCode))
		{				
			if($this->checkCurrentCoupon())
			{					
				$this->_couponOnly = true;				
				$this->processProducts();
			}
			else
			{
				$this->_couponOnly = false;	
				$this->removeItembyProductId($this->_productId, 'checkCurrentCoupon()->processCouponCodeRequests()');
			}
		}
		else
		{
				
			$this->_couponOnly = false;	
			$this->removeItembyProductId($this->_productId, 'NO_>$this->_couponCode');
		}
	}
	
	public function getFreeGifts()
	{
		$this->setCartFunctions();
		return $this->getProductInsertCollection();	
	}
	
	protected function processChooseGift()
	{
		$this->getProductGroupCollection();
		$this->getProductInsertCollection();
		foreach ($this->_items as $item)
		{
			if($this->checkGroupbyExceptionType($item['product_id']) && $this->checkOrphanItem($item['item_id'], $item['product_id'])){
				
//				if($this->checkSingleInsertProduct())
//				{						
//					$this->processProducts();					
//				}
//				else{
//					//do multiple here		
//				}
				
			}
			elseif(in_array($item['product_id'], $this->getProductInsertCollection()))
			{					
				$this->removeItembyItemId($item['item_id'], 'processXforYRequests()-PromoId->'.$this->_promoGroupId.'->ProductId'.$item['product_id']);
			}
		}		
	}
	
	protected function processXforYRequests()
	{
		$this->getProductGroupCollection();
		$this->getProductInsertCollection();
				
		foreach ($this->_items as $item)
		{						
			if( $this->checkGroupbyExceptionType($item['product_id']) && $this->checkOrphanItem($item['item_id'], $item['product_id'])){
							
				//if($this->checkSingleInsertProduct())
				//{						
					$this->processProducts();					
//				}
//				else{
//					echo '<br>do multiple here';		
//				}
				
			}
			elseif(in_array($item['product_id'], $this->getProductInsertCollection()))
			{					
				$this->removeItembyItemId($item['item_id'], 'processXforYRequests()-PromoId->'.$this->_promoGroupId.'->ProductId'.$item['product_id']);
			}	
			
		}
	}
	
	protected function checkOrphanItem($itemId, $productId)
	{		
		$val = true;
		if(in_array($productId, $this->getProductInsertCollection())){
				
			if(in_array($itemId, $this->getItemIdxCollection())){				
												
				$match = array_intersect($this->getProductGroupCollection(), $this->getCurrentCartProductCollection());
				if(count($match) <= 0)
				{					
					$val = false;
				}
			}
		}
		
		return $val;	
	}
	
	protected function validatePromoId($productId)
	{			
		$checkEx =  false;
		if(!empty($this->_currentGroupCollection))				
		{				
			foreach ($this->_items as $item) {	
				foreach ($this->_currentGroupCollection as $entityId)
				{					
					if($entityId == $item['product_id'])
					{						
						$checkEx = true;
					}
				}						
			}
			if(!$checkEx)
			{
				$this->removeItembyProductId($productId, 'Validate Promo ID FAIL');	
			}
		}
	}
	
	protected function processPriceRequests()
	{		
		$valueCollection = Mage::getresourceModel('cartex/cart_value_collection')
			->addFilter('wdc_attribute_id', $this->_currentPromoCollection->getCartexId());
		if($valueCollection)
		{				
			foreach ($valueCollection as $checkVal)
			{				
				$this->validateExpressions($checkVal);		
			}		
		}
	}
	
	protected function validateExpressions($val)
	{		
		$this->getProductInsertCollection();
		
		switch($val->getQualStatement())
		{
			case 'between';				
				if($this->_cartTotal > $val->getMinVal() && $this->_cartTotal < $val->getMaxVal())
				{
					if($this->_xy){
						$this->processXforYRequests();	
					}
					else
					{
						$this->processProducts();	
					}
				}
				else{
					//added 082010 not sure if I need to process here, but it seems to work
					foreach ($this->_items as $item)
					{						
						if(in_array($item['product_id'], $this->getProductInsertCollection()))
						{				
							$this->removeItembyItemId($item['item_id'], 'validateExpressions');
						}							
					}
			}
				break;
			
		}
	}
	
	protected function getCartProductIdCollection()
	{
			foreach ($this->_items as $item)
			{
				$this->_currentCartProductIdCollection[] = (int)$item['product_id'];		
			}
			
			return $this->_currentCartProductIdCollection;	
			
	}
	
//	protected function getProductGroupCollection()
//	{
//		$this->_currentGroupCollection = array();
//		$groupCollection = Mage::getresourceModel('cartex/cart_groups_collection')
//			->addFilter('entity_type_id', $this->_entityTypeId)
//			->addFilter('wdc_attribute_id', $this->_promoGroupId);
//		
//		foreach ($groupCollection as $item)
//		{
//			////Mage::helper('errorlog')->insert('getProductGroupCollection()', 'attrib->'.$item->getAttributeSetId());
//			
//			if($this->_exceptType == 2){
//				$this->_currentGroupCollection[] = (int)$item->getAttributeSetId();
//			}
//			else{
//				$this->_currentGroupCollection[] = (int)$item->getEntityId();
//			}		
//		}		
//		return $this->_currentGroupCollection;
//	}
	
	protected function checkSingleInsertProduct()
	{		
		$this->getProductInsertCollection();
		$val = false;	
		
		if(count($this->_currentItemCollection) === 1)
		{			
			foreach ($this->_currentItemCollection as $productId)
			{					
				$this->_productId =  $productId;
				$val = true;	
			}	
		}
		return $val;	
	}
}