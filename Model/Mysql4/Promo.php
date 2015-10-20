<?php

class Wdc_Cartex_Model_Mysql4_Promo extends Mage_Core_Model_Mysql4_Abstract
{
	/**
	 * Initialize resource model
	 */
	public function _construct()
	{
		$this->_init('orders/promo', 'entity_id');
	}
	
}