<?php

class Wdc_Cartex_Model_Mysql4_Cart_Coupon extends Mage_Core_Model_Mysql4_Abstract
{
	protected function _construct()
	{
		$this->_init('cartex/cart_coupon', 'coupon_id');
		
	}	
	
	public function fetchbyCartexId($cartexId)
	{
		$sql = $this->_getReadAdapter()->select()
			->from($this->getMainTable(), 'coupon_id')		
			->where('cartex_id=?', $cartexId);		
		return $this->_getReadAdapter()->fetchCol($sql);		
	}
	
	public function fetchCouponCodebyCartexId($cartexId)
	{
		$sql = $this->_getReadAdapter()->select()
			->from($this->getMainTable(), 'coupon_code')		
			->where('cartex_id=?', $cartexId);		
		return $this->_getReadAdapter()->fetchCol($sql);		
	}
	
	public function fetchCouponIdbyCouponCode($couponCode)
	{
		$sql = $this->_getReadAdapter()->select()
			->from($this->getMainTable(), 'coupon_id')		
			->where('coupon_code=?', $couponCode);		
		return $this->_getReadAdapter()->fetchCol($sql);		
	}
	
	public function fetchCartexIdbyCouponId($couponId)
	{
		$sql = $this->_getReadAdapter()->select()
			->from($this->getMainTable(), 'cartex_id')		
			->where('coupon_id=?', $couponId);		
		return $this->_getReadAdapter()->fetchRow($sql);		
	}	
	
	public function checkCouponExist($cartexId, $couponId)
	{		
		$val = false;
		$att = $this->fetchCartexIdbyCouponId($couponId);
		if($att){
			if($att['cartex_id'] == $cartexId)
			{
				$val = true;	
			}
		}		
		return $val;		
	}
	
	public function getCouponIdbyRuleId($ruleId)
	{
		$sql = $this->_getReadAdapter()->select()
			->from($this->getTable('salesrule/coupon'), array('coupon_id'))		
			->where('rule_id=?', $ruleId);		
		$id = $this->_getReadAdapter()->fetchRow($sql);		
		if($id){		
			return $id['coupon_id'];
		}
	}
	
	public function updateRuleActions($rulefromId, $ruletoId)
	{	 
	$ruleModel = Mage::getModel('salesrule/rule')->load($rulefromId);	 
                        
		$data = array(
			'actions_serialized' => $ruleModel->getActionsSerialized(),
			'conditions_serialized' => $ruleModel->getConditionsSerialized(),
			//'conditions_serialized' => 'test',
			);
		$condition = $this->_getWriteAdapter()->quoteInto('rule_id=?', $ruletoId);
		$this->_getWriteAdapter()->update($this->getTable('salesrule/rule'), $data, $condition);
		return $this;		
	}

}