<?php
class Wdc_Cartex_Model_Mysql4_Promo_Product extends Mage_Core_Model_Mysql4_Abstract
{
	protected function _construct()
	{
		$this->_init('cartex/promo_product', 'promo_product_id');
	}	
	
	public function getCartexProducts($cartexId)
	{
		$sql = $this->_getReadAdapter()->select()
			->from($this->getTable('cartex/promo_product'), array('entity_id'));
			//->where('template_id = ?', $cartexId);			
		return $this->_getReadAdapter()->fetchCol($sql);		
	}	
	

}