<?php

class Wdc_Cartex_Block_Product_Promoview extends Mage_Catalog_Block_Product_View
{	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function getPromoProduct()
	{
		return Mage::getModel('cartex/product_promo')->getPromoProduct();
	}
	
	
}
