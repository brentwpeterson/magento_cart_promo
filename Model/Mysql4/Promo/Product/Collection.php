<?php

class Wdc_Cartex_Model_Mysql4_Promo_Product_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	
	public function _construct()
	{
		$this->_init('cartex/promo_product');
	}

}