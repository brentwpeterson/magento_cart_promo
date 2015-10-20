<?php

class Wdc_Cartex_Model_Rules_Buyxgety extends Wdc_Cartex_Model_Cartex
{	
	
	public function __construct()
	{
		$this->_items = Mage::getresourceModel('cartex/cart_item')->getCurrentCart();
	}
	
	public function processRule($promo)
	{
		$this->setCurrentPromoCollection($promo);	
		
		$groupCollection = Mage::getresourceModel('cartex/cart_groups_collection')
			->addFilter('wdc_attribute_id', $promo->getId())
			->addFilter('entity_type_id', $this->_entityTypeId);
			
		if($groupCollection)
		{				
			foreach ($groupCollection as $group)
			{
				echo '<br>help!';	
			}	
		}	
	}	
	
}