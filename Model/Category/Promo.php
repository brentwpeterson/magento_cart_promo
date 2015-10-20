<?php

class Wdc_Cartex_Model_Category_Promo extends Wdc_Cartex_Model_Cartex
{

	protected function _construct()
	{
		//$this->_init('cartex/cart_value');		
	}
	
	public function checkCategory()
	{
		$val = false;
		
		foreach ($this->getActivePromoGroups() as $promo)
		{			
			if(in_array($promo->getPromoType(), $this->getQualifyIds())){
				if($promo->getExceptionTypeId() == 0){					
					foreach ($this->_getGroups($promo->getCartexId(), Mage::getModel('eav/entity_type')->loadByCode('catalog_product')->getId()) as $productId){
						if(in_array($productId->getEntityId(), $this->getProducts())){
							$val = true;
							break;
						}
						
					}
				}
				elseif($promo->getExceptionTypeId() == 1){
					
				
					foreach ($this->_getGroups($promo->getCartexId(), Mage::getModel('eav/entity_type')->loadByCode('catalog_category')->getId()) as $categoryId){					
						if($categoryId->getEntityId() == $this->getCategory()->getId()){
							$val = true;
							break;
						}
					}
				}
			}
		}		
		
		return $val;			
	}	
	
	protected function getCategory()
	{
		return Mage::registry('current_category');
	}
	
	protected function getProducts()
	{
		$products = array();
		foreach ($this->getCategory()->getProductCollection() as $product)
		{
			$products[] = $product->getId();	
		}	
		return $products;
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
	
	
	
}