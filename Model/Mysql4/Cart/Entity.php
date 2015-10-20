<?php

class Wdc_Cartex_Model_Mysql4_Cart_Entity extends Mage_Core_Model_Mysql4_Abstract
{

	protected function _construct()
	{
		$this->_init('cartex/cart_entity', 'cartex_id');
		
	}	
	
	public function fetchActiveGroups()
	{
		$read = $this->_getReadAdapter();
		$select = $read->select()
			->from(array('qo'=>$this->getTable('cartex/cart_entity')), array('cartex_id'))
			->where('qo.is_active=1');		
			$result = $read->fetchCol($select);
		if($result)
		{
			return $result;
		}
		
	}
	
	public function fetchbyEntityId($entityId, $entityTypeCode='catalog_product')
	{
		$val = false;
		$read = $this->_getReadAdapter();
		$select = $read->select()
			->from(array('a'=>$this->getTable('cartex/cart_entity')), array('cartex_id'))
			->join(
				array('e' => $this->getTable('eav/entity_type')),
				'e.entity_type_id=a.entity_type_id',
				array()
				)			
			->where('a.entity_id=?', $entityId)
			->where('e.`entity_type_code`=?', $entityTypeCode)
			->limit(1);
			
		$result = $read->fetchCol($select);
		
		if($result)
			{
			foreach ($result as $id)
				{
				$val = $id;	
				}	
			}
		return $val;
		
	}
	
}