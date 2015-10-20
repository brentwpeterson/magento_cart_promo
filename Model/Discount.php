<?php

class Wdc_Cartex_Model_Discount extends Wdc_Cartex_Model_Abstract
{
	protected $_discount_itemid;
	protected $_discount_amount;
	
	protected function processDiscount($productId)
	{		
		$items = $this->getCurCart()->getAllItems();
		
		foreach ($items as $item)
		{
			if($item->getProductId() == $productId){
										
				$optionModel = Mage::getModel('sales/quote_item_option');
				$optionModel->setItemId($item->getId());
				$optionModel->setProductId($item->getProductId());
				$optionModel->setCode('product_qty_'.$item->getProductId());
				$optionModel->setValue('1');
				$optionModel->save();
								
				$item->setCustomPrice(($item->getPrice() - $this->_discount_amount));
				$item->save();
			}		
		}	
	}	
	
	protected function getCurCart()
	{
		return Mage::getModel('checkout/session')->getQuote();
	}
}