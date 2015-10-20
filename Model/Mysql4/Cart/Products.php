<?php

class Wdc_Cartex_Model_Mysql4_Cart_Products extends Mage_Core_Model_Mysql4_Abstract
{
	protected function _construct()
	{
		$this->_init('cartex/cart_products', 'promo_product_id');
	}
	
	public function fetchbyAttributeId($wdcAttributeId)
	{
		$sql = $this->_getReadAdapter()->select()
			->from($this->getMainTable(), array('entity_id'))			
			->where('wdc_attribute_id=?', $wdcAttributeId);		
		return $this->_getReadAdapter()->fetchCol($sql);		
	}	
	
	public function fetchbyParentId($parentId)
	{
		$sql = $this->_getReadAdapter()->select()
			->from($this->getMainTable(), array('entity_id'))			
			->where('parent_id=?', $parentId);		
		return $this->_getReadAdapter()->fetchCol($sql);		
	}
	
	
	public function fetchIdbyEntityId($wdcAttributeId, $entityId)
	{
		$sql = $this->_getReadAdapter()->select()
			->from($this->getMainTable(), array('promo_product_id'))
			->where('entity_id=?', $entityId)		
			->where('wdc_attribute_id=?', $wdcAttributeId);		
		return $this->_getReadAdapter()->fetchCol($sql);		
	}
	

	
	public function deletebyEntityId($wdcAttributeId, $entityId)
	{		
		$this->_getWriteAdapter()->delete($this->getMainTable(), array("entity_id = {$entityId}", "wdc_attribute_id = {$wdcAttributeId}"));
	}
	
	public function checkProductExist($wdcAttributeId, $entityId)
	{
		$val = false;
		$product = $this->fetchIdbyEntityId($wdcAttributeId, $entityId);
		if($product)
		{
			$val = true;	
		}			
		return $val;		
	}
	
	public function getPromoIdbyProduct($productId)
	{
		$val = 0;
		$sql = $this->_getReadAdapter()->select()
			->from($this->getMainTable(), array('wdc_attribute_id'))
			->where('entity_id=?', $productId)	
			->limit(1);		
		$col =  $this->_getReadAdapter()->fetchCol($sql);
		
		if($col){
			$val = $col[0]; //['wdc_attribute_id'];			
		}
		
		return $val;
	}
}