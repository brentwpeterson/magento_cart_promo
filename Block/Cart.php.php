<?php

class Wdc_Cartex_Block_Entity extends Mage_Core_Block_Abstract
{	
	private $_priceBlock = array();
	private $_priceBlockDefaultTemplate = 'catalog/product/price.phtml';
	private $_tierPriceDefaultTemplate  = 'catalog/product/view/tierprices.phtml';
	private $_priceBlockTypes = array();
	protected $_promoId;

	public function __construct()
	{
		parent::__construct();		
	}	

	protected function getPromoCollection()
	{	
		return Mage::getresourceModel('cartex/cart_products_collection')
		->addFilter('wdc_attribute_id', $this->_promoId);				
	}
	
	protected function getGroupCount()
	{
		return count($this->getPromoCollection());	
	}
	
	public function getPriceHtml($product, $displayMinimalPrice = false, $idSuffix='')
	{
		return $this->_getPriceBlock($product->getTypeId())
		->setTemplate($this->_getPriceBlockTemplate($product->getTypeId()))
		->setProduct($product)
		->setDisplayMinimalPrice($displayMinimalPrice)
		->setIdSuffix($idSuffix)
		->toHtml();
	}
	
	protected function _getPriceBlock($productTypeId)
	{
		if (!isset($this->_priceBlock[$productTypeId])) {
			$block = 'catalog/product_price';
			if (isset($this->_priceBlockTypes[$productTypeId])) {
				if ($this->_priceBlockTypes[$productTypeId]['block'] != '') {
					$block = $this->_priceBlockTypes[$productTypeId]['block'];
				}
			}
			$this->_priceBlock[$productTypeId] = $this->getLayout()->createBlock($block);
		}
		return $this->_priceBlock[$productTypeId];
	}
	
	protected function _getPriceBlockTemplate($productTypeId)
	{
		if (isset($this->_priceBlockTypes[$productTypeId])) {
			if ($this->_priceBlockTypes[$productTypeId]['template'] != '') {
				return $this->_priceBlockTypes[$productTypeId]['template'];
			}
		}
		return $this->_priceBlockDefaultTemplate;
	}

}
