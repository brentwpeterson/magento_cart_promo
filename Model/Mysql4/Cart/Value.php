<?php

class Wdc_Cartex_Model_Mysql4_Cart_Value extends Mage_Core_Model_Mysql4_Abstract
{


	protected function _construct()
	{
		$this->_init('cartex/cart_value', 'value_id');
		
	}
	
	public function fetchValuebyId($valueId)
	{
		$read = $this->_getReadAdapter();
		$select = $read->select()
			->from(array('qo'=>$this->getTable('cartex/cart_value')), array('id'=>'value_id', 'value'))
			->where('qo.value_id=?', $valueId);		
		$result = $read->fetchAll($select);
		if($result)
		{
			return $result;
		}
		
	}
	
	public function fetchbyAttributeId($wdcAttributeId)
	{
		$sql = $this->_getReadAdapter()->select()
			->from($this->getMainTable(), array('value_id'))			
			->where('wdc_attribute_id=?', $wdcAttributeId);		
		return $this->_getReadAdapter()->fetchCol($sql);		
	}
		
	public function fetchbyProductId($productId)
	{
		$read = $this->_getReadAdapter();
		$select = $read->select()
			->from(array('qo'=>$this->getTable('cartex/cart_value')), array('id'=>'value_id', 'wdc_attribute_id', 'value'))
			->where('qo.wdc_exception_item_id=?', $productId);		
		$result = $read->fetchAll($select);
		if($result)
		{
			return $result;
		}
		
	}
	
	public function fetchbyPromoGroupId($attributeId)
	{
		$read = $this->_getReadAdapter();
		$select = $read->select()
			->from(array('qo'=>$this->getTable('cartex/cart_value')), array('id'=>'value_id', 'wdc_exception_item_id', 'wdc_attribute_id', 'wdc_exception_group_id', 'value'))
			->where('qo.wdc_attribute_id=?', $attributeId);		
		$result = $read->fetchAll($select);
		if($result)
		{
			return $result;
		}
		
	}
	
	public function checkValueExist($wdcAttributeId)
	{
		$val = false;
		$product = $this->fetchbyAttributeId($wdcAttributeId);
		if($product)
		{
			$val = true;	
		}			
		return $val;		
	}
	
}