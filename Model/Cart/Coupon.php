<?php

class Wdc_Cartex_Model_Cart_Coupon extends Mage_Core_Model_Abstract
{
	protected $_couponCode;	

	protected function _construct()
	{
		$this->_init('cartex/cart_coupon');	
		$this->_couponCode = Mage::getModel('checkout/session')
			->getQuote()->getCouponCode();	
	}		
	
	public function isCoupon()
	{		
		if(Mage::getResourceModel('cartex/cart_coupon')->fetchCouponIdbyCouponCode($this->_couponCode)){
			return true;			
		}
		else
		{
			return false;	
		}
	}
	
	public function isIncommCoupon($couponCode)
	{		
		
		Mage::log(stripos($couponCode, 'DEW'));
		if(stripos($couponCode, 'DEW') === false){
			return false;				
		}
		else
		{
			return true;
		}
	}
	
	
}

