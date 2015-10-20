<?php

class Wdc_Cartex_Model_Product_Promo extends Wdc_Cartex_Model_Cartex
{
	protected $_promoId = 0;
	
	public function checkProduct()
	{
		$val = false;		
		foreach ($this->getActivePromoGroups() as $promo)
		{			
			if(in_array($promo->getPromoType(), $this->getQualifyIds())){
				if($promo->getExceptionTypeId() == 0){					
					foreach ($this->_getGroups($promo->getCartexId(), Mage::getModel('eav/entity_type')->loadByCode('catalog_product')->getId()) as $productId){
						if($productId->getEntityId() == $this->getProduct()->getId()){
							$val = true;
							break;
						}						
					}
				}			
			}
		}			
		return $val;			
	}	
	
	public function checkListProduct($productId)
	{
		$val = false;		
		foreach ($this->getActivePromoGroups() as $promo)
		{	
			if(in_array($promo->getPromoType(), $this->getQualifyIds())){
				if($promo->getExceptionTypeId() == 0){						
					foreach ($this->_getGroups($promo->getCartexId(), Mage::getModel('eav/entity_type')->loadByCode('catalog_product')->getId()) as $_productId){
						if($_productId->getEntityId() == $productId){							
							$val = true;
							break;
						}						
					}
				}			
			}
		}			
		return $val;			
	}
	
	protected function getProduct()
	{
		return Mage::registry('current_product');
	}
	
	protected function getQualifyIds()
	{
		return array(1,3,6,7,11);	
	}
	
	protected function _getGroups($promoId, $entityTypeId)
	{
		return Mage::getresourceModel('cartex/cart_groups_collection')
			->addFilter('entity_type_id', $entityTypeId)
			->addFilter('wdc_attribute_id', $promoId);	
	}
	
	public function getPromoProduct()
	{
		$_product = null;
		foreach ($this->getActivePromoGroups() as $promo)
		{				
			if(in_array($promo->getPromoType(), $this->getQualifyIds())){
				if($promo->getExceptionTypeId() == 0){					
					foreach ($this->_getGroups($promo->getCartexId(), Mage::getModel('eav/entity_type')->loadByCode('catalog_product')->getId()) as $productId){
						if($productId->getEntityId() == $this->getProduct()->getId()){
							$item = Mage::getresourceModel('cartex/cart_item')->fetchbyAttributeId($promo->getCartexId());
							foreach ($item as $promoProduct){
								
								$_product = Mage::getModel('catalog/product')->load($promoProduct);
								break;
							}
						}						
					}
				}			
			}
		}			
		return $_product;
	}
	
	protected function getInsertProduct($promo)
	{
			
	}
	
}