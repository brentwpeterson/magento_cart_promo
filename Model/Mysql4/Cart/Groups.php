<?php

class Wdc_Cartex_Model_Mysql4_Cart_Groups extends Mage_Core_Model_Mysql4_Abstract
{

	protected function _construct()
	{
		$this->_init('cartex/cart_groups', 'wdc_id');
	}
	
	public function fetchOverridebyId($attributeSetId, $wdcAttributeId)
	{
		$read = $this->_getReadAdapter();
		$select = $read->select()
			->from(array('qo'=>$this->getTable('cartex/cart_groups')), array('id'=>'wdc_id', 'wdc_override'))
			->where('qo.attribute_set_id=?', $attributeSetId)		
			->where('qo.wdc_attribute_id=?', $wdcAttributeId);
		$result = $read->fetchAll($select);
		if($result)
		{
			return $result;
		}
		
	}
	
	public function fetchbyCartexId($wdcAttributeId)
	{
		$sql = $this->_getReadAdapter()->select()
			->from($this->getMainTable(), array('entity_id'))		
			->where('wdc_attribute_id=?', $wdcAttributeId);
			
		return $this->_getReadAdapter()->fetchCol($sql);		
	}
	
	public function fetchbyCartexIdEntitytype($wdcAttributeId, $entityTypeId)
	{		
		
		////Mage::helper('errorlog')->insert('fetchbyCartexIdEntitytype', $wdcAttributeId.' etype->'.$entityTypeId);	
		$sql = $this->_getReadAdapter()->select()
			->from($this->getMainTable(), array('entity_id'))		
			->where('wdc_attribute_id=?', 3) //$wdcAttributeId)
			->where('entity_type_id=?', 4); //$entityTypeId);
		
		$rows = $this->_getReadAdapter()->fetchCol($sql);	
		
		foreach ($rows as $row)
		{
			////Mage::helper('errorlog')->insert('fetchbyCartexIdEntitytype', $row);	
		}	
	}
	
	public function fetchAttributeSetbyCartexId($wdcAttributeId)
	{
		$sql = $this->_getReadAdapter()->select()
			->from($this->getMainTable(), array('attribute_set_id'))		
			->where('wdc_attribute_id=?', $wdcAttributeId);
		
		return $this->_getReadAdapter()->fetchCol($sql);		
	}
	
	public function fetchbyAttributeId($wdcAttributeId, $entityTypeId=0, $attributeSetId=0)
	{
		$sql = $this->_getReadAdapter()->select()
			->from($this->getMainTable(), array('entity_id'))
			->where('attribute_set_id=?', $attributeSetId)		
			->where('wdc_attribute_id=?', $wdcAttributeId)
			->where('entity_type_id=?', $entityTypeId);		
		return $this->_getReadAdapter()->fetchCol($sql);		
	}	
	
	public function getPromoIdfromProductId($entityId, $entityTypeId=10)
	{
		$sql = $this->_getReadAdapter()->select()
			->from($this->getMainTable(), array('wdc_attribute_id'))
			->where('entity_id=?', $entityId)		
			->where('entity_type_id=?', $entityTypeId);		
		return $this->_getReadAdapter()->fetchCol($sql);		
	}
	
	public function fetchIdbyEntityId($wdcAttributeId, $entityId, $entityTypeId=0)
	{
		$sql = $this->_getReadAdapter()->select()
			->from($this->getMainTable(), array('wdc_id'))
			->where('entity_id=?', $entityId)		
			->where('wdc_attribute_id=?', $wdcAttributeId)
			->where('entity_type_id=?', $entityTypeId);		
		return $this->_getReadAdapter()->fetchCol($sql);		
	}
	
	public function deletebyEntityId($wdcAttributeId, $entityId, $entityTypeId=0)
	{		
		$this->_getWriteAdapter()->delete($this->getMainTable(), 
			array("wdc_attribute_id = {$wdcAttributeId}", "entity_id = {$entityId}", "entity_type_id = {$entityTypeId}"));
	}
	
	public function deletebyAttributeSetId($wdcAttributeId, $attributeSetId)
	{		
		$this->_getWriteAdapter()->delete($this->getMainTable(), 
			array("wdc_attribute_id = {$wdcAttributeId}", "attribute_set_id = {$attributeSetId}"));
	}
	
	public function checkProductExist($wdcAttributeId, $entityId, $entityTypeId=0)
	{
		$val = false;
		$product = $this->fetchIdbyEntityId($wdcAttributeId, $entityId, $entityTypeId);
		if($product)
			{
			$val = true;	
			}			
		return $val;		
	}
	
	public function checkAttributeSetExist($wdcAttributeId, $attributeSetId, $entityTypeId=0)
	{
		$val = false;
		$att = $this->fetchbyAttributeId($wdcAttributeId, $entityTypeId, $attributeSetId);
		if($att)
		{
			$val = true;	
		}			
		return $val;		
	}

}