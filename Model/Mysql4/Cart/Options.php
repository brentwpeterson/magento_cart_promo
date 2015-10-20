<?php

class Wdc_Cartex_Model_Mysql4_Cart_Options extends Mage_Core_Model_Mysql4_Abstract
{

	protected function _construct()
	{
		$this->_init('cartex/cart_options', 'wdc_entity_id');
	}

}
