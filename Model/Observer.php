<?php

class Wdc_Cartex_Model_Observer
{		
	public function wdcSalesQuoteRemoveItem($observer){
			
		$item = $observer->getEvent()->getQuoteItem();			
		Mage::getresourceModel('cartex/cart_idx')->deletebyItemId($item->getId(), 1, 'observer');		
	}	
}