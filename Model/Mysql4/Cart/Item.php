<?php

class Wdc_Cartex_Model_Mysql4_Cart_Item extends Mage_Core_Model_Mysql4_Abstract
{
	protected function _construct()
	{
		$this->_init('cartex/cart_item', 'wdc_id');		
	}
	
	public function fetchListbyAttrib($attributeId)
	{
		$read = $this->_getReadAdapter();
		$select = $read->select()
			->from(array('qo'=>$this->getTable('cartex/cart_item')), array('id'=>'entity_id'))
			->where('qo.wdc_attribute_id=?', $attributeId);		
		$result = $read->fetchAll($select);
		if($result)
		{
			return $result;
		}		
	}
	
	public function fetchbyAttributeId($wdcAttributeId)
	{
		$sql = $this->_getReadAdapter()->select()
			->from($this->getMainTable(), array('entity_id'))			
			->where('wdc_attribute_id=?', $wdcAttributeId);
			
		return $this->_getReadAdapter()->fetchCol($sql);		
	}	
	
	public function fetchExceptionbyAttributeId($wdcAttributeId)
	{
		$sql = $this->_getReadAdapter()->select()
			->from($this->getMainTable(), array('entity_id'))			
			->where('wdc_attribute_id !=?', $wdcAttributeId);
		
		return $this->_getReadAdapter()->fetchCol($sql);		
	}	
	
	public function fetchIdbyEntityId($wdcAttributeId, $entityId)
	{
		$sql = $this->_getReadAdapter()->select()
			->from($this->getMainTable(), array('wdc_id'))
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
	
	public function getCurrentCart()
	{
		$quoteId = Mage::getSingleton('checkout/session')->getQuoteId();
		 
		$select = $this->_getReadAdapter()->select()
			->from(array('qo'=>$this->getTable('sales/quote_item')), array('*'))
			->where('qo.quote_id=?', $quoteId);		
		$result = $this->_getReadAdapter()->fetchAll($select);
		if($result)
		{
			return $result;
		}
		else
		{
			$nought = array();
			return $nought;
		}		
	}

}