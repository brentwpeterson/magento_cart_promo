<?php

class Wdc_Cartex_Block_Product_Promo extends Mage_Core_Block_Abstract
{	
	protected $_promoId = 0;
	protected $_promoproductId =0;
	protected $_view = false;

	public function __construct()
	{
		//$this->setPromoId();
		parent::__construct();		
	}
	
	public function getProduct()
	{
		return Mage::registry('current_product');
	}
	
	protected function setPromoId()
	{
		$collection = Mage::getresourceModel('cartex/cart_groups')->getPromoIdfromProductId($this->getProduct()->getId());				
		
		foreach ($collection as $promoId)
		{
			$this->_promoId = $promoId;
			break;
		}
		
		if($this->_promoId != 0)
		{
			$this->_view = true;
		}		
		
		return $this->_promoId;
		
	}
	
//	protected function _toHtml()
//	{	
//		//if($this->_view){
//			
//			$html = '';	
//			
//			//$html.= '<div style="border: solid thin green; padding-left:5px; color:red; text-align:center;">';
//			//$html.= 'You will receive a '.$this->getPromoProduct().' when purchasing this product';
//			$html.= 'You will receive a  when purchasing this product';
//			//$html.= '</div>';
//			
//			return $html;
//		//}
//	}
	
	protected function checkProductSale($productId)
	{
		$val = true;
		$product = Mage::getModel('catalog/product')->load($productId);
		if($product->getStockItem()->getIsInStock() != 1)
		{
			$val = false;
		}	
		return $val;
	}
	
	protected function getPromoProduct()
	{
		$itemCollection = Mage::getresourceModel('cartex/cart_item_collection')
			->addFilter('wdc_attribute_id', $this->_promoId);	
		
		
		foreach ($itemCollection as $item)
		{
			
				$this->_promoproductId = $item->getEntityId();
				break;
									
		}
		
		if($this->_promoproductId != 0){
			$product = Mage::getModel('catalog/product')->load($this->_promoproductId);
			if($product->getStockItem()->getIsInStock() == 1)
			{
				return $product->getName();
			}	
			
		}
		else{
			return $this->getProduct()->getId();
		}
		
	}
	
	
	
	
	
	
}
