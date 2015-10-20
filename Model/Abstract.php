<?php

class Wdc_Cartex_Model_Abstract extends Mage_Core_Model_Abstract
{
	public function checkVersion()
	{
		$val = 1;
		
		if(Mage::getVersion() < 1.3){
			$val = 0;
		}
		elseif(Mage::getVersion() > 1.3){
			if(Mage::getVersion() == '1.4.0.1'){
				$val = 1;
			}
			else{
				$val = 2;
			}
		}	
		
		return $val;
	}
	
	public function getCart()
	{
		return Mage::getresourceModel('cartex/cart_item')->getCurrentCart();
	}
}
