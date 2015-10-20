<?php

class Wdc_Cartex_Block_Product_View extends Wdc_Cartex_Block_Entity
{	
	private $_priceBlock = array();
	private $_priceBlockDefaultTemplate = 'catalog/product/price.phtml';
	private $_tierPriceDefaultTemplate  = 'catalog/product/view/tierprices.phtml';
	private $_priceBlockTypes = array();
	protected $_promoId;
	protected $_view = false;

	public function __construct()
	{
		parent::__construct();		
	}
	
	public function getProduct()
	{
		return Mage::registry('current_product');
	}
	
	protected function setPromoId()
	{
		$this->_promoId = Mage::getresourceModel('cartex/cart_products')->getPromoIdbyProduct($this->getProduct()->getId());				
		
		if($this->_promoId != 0)
		{
			$this->_view = true;
		}		
		
		return $this->_promoId;
		
	}
	
	protected function _toHtml()
	{		
		$this->setPromoId();
		if($this->_view){
			return $this->drawPromoGroup();			
		}
	}
	
	protected function drawPromoGroup()
	{
		
		$promo = Mage::getModel('cartex/cart_entity')->load($this->_promoId);
		
		//	$html = '<div style="float:left;  border:solid thin red; padding:0 0 3px 0;">';
		$html = '<div style="float:left; position:relative; width:730px; padding:0 0 3px 0;"><span style="font-size:large;">';
		$html.= $promo->getDescription();
		$html.= '</span> &nbsp;';
		$html.= ' <button class="button" onclick="setLocation(\''.$this->getAddUrl().'\')">';
		$html.= '<span>Add all to Cart</span></button></div>';
		$html.= '<div style="float:left; border:1px solid #ddd; position:relative; background:#cccccc; width:730px;">';
		
		$divw = $this->getDivWidth($this->getGroupCount());	
		
		$groupedproductCollection = $this->getPromoCollection();
		
		$j=0;
		foreach ($groupedproductCollection as $item)
		{	
			$pproduct = Mage::getModel('catalog/product')->load((int)$item->getEntityId());
			$html.= '<div style="float:left; width:'.$divw.'px; padding:0 2px 0 4px; height:120px; ';
			if($j == (count($groupedproductCollection)-1)){
				$html.= '">';			
			}
			else
			{
				$html.= ' border-right:dotted thin red; ">';			
			}
			$html.= '<div style="height:100px; font-weight:bold;">';
			$html.= $pproduct->getName().'</div><div style="height:20px; border-top:dotted thin black;">'.$this->getPriceHtml($pproduct).'</div></div>';
			$j++;
		}
		
		$html.= '</div>';
		return $html;	
	}
	
	protected function getAddUrl()
	{
		return '/cartex/index/add/promoid/'.$this->_promoId.'./';	
	}
	
	protected function getDivWidth($divcnt)
	{
		if($divcnt != 0)
		{
			$padcnt = $divcnt * 7;
			$d = (730 - $padcnt)  / $divcnt;
			if($d < 100)
			{
				$d =100;	
			}	
			return $d;
		}
	}
	
	public function getPromo()
	{
		return 'Giant Test';	
	}
}
