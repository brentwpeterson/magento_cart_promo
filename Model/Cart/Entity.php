<?php

class Wdc_Cartex_Model_Cart_Entity extends Mage_Core_Model_Abstract
{

	protected function _construct()
	{
		$this->_init('cartex/cart_entity');		
	}	
	
	public function loadbyCode($code)
	{
		$sql = "SELECT cartex_id FROM wdc_cartex_exception_entity where promo_code = '".$code."'";
		$result = $this->_read->fetchRow($sql);
		if($result)
		{
			$this->_attributeId = $result['cartex_id'];
			return $this->_attributeId;
		}
	}
	
}