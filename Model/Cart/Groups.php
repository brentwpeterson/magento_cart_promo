<?php

class Wdc_Cartex_Model_Cart_Groups extends Mage_Core_Model_Abstract
{

	protected function _construct()
	{
		$this->_init('cartex/cart_groups');		
	}	
	
	public function getGroupsbycartexId($entityTypeId, $cartexId)
	{
		
		return  Mage::getResourceModel('cartex/cart_groups')->fetchbyCartexIdEntitytype($cartexId, $entityTypeId);
//		
//		foreach ($collection as $item)
//		{
//			//Mage::helper('errorlog')->insert('getGroupsbycartexId(', 'rets');
//		}
	}
	
}