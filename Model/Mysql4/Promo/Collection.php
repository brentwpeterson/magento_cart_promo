<?php

class Wdc_Cartex_Model_Mysql4_Promo_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	
	public function _construct()
	{
		$this->_init('orders/promo');
	}
	
	public function addCustomers()
	{
			$this->getSelect()->join(
				array('order_table' => $this->getTable('sales/order')),
				'main_table.order_id = order_table.order_id',
				array()
				);
	
		return $this;
	}
}