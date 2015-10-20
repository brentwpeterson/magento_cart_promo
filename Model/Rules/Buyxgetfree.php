<?php

/**
 * 
 * Need to update insert Coupon for 1.4
 * **/

class Wdc_Cartex_Model_Rules_Buyxgetfree extends Wdc_Cartex_Model_Cartex
{	
	
	public function __construct()
	{
		$this->_items = Mage::getresourceModel('cartex/cart_item')->getCurrentCart();
	}
	
	public function processRule($promo)
	{
		$this->setCurrentPromoCollection($promo);
		//$this->_promoGroupId = $promo->getId();
		//$this->_entityTypeId = $promo->getEntityTypeId();
		$valueCollection = Mage::getresourceModel('cartex/cart_value_collection')
			->addFilter('wdc_attribute_id', $promo->getId());
		if($valueCollection)
		{				
			foreach ($valueCollection as $checkVal)
			{			
				$this->validateExpressions($checkVal);		
			}		
		}	
	}
	
	protected function validateExpressions($val)
	{			
		switch($val->getQualStatement())
		{
			case 'between';				
				if($this->getQualCartQty() >= $val->getMinVal() && $this->getQualCartQty() <= $val->getMaxVal())
				{					
					$this->processProducts();					
				}
				else{
					//added 082010 not sure if I need to process here, but it seems to work
					foreach ($this->_items as $item)
					{						
						if(in_array($item['product_id'], $this->getProductInsertCollection()))
						{				
							$this->removeItembyItemId($item['item_id'], 'validateExpressions');
						}							
					}
				}
				break;
			
		}
	}
	
	protected function getQualCartQty()
	{		
		$qty = 0;
		foreach ($this->_items as $item)
		{				
			if(in_array($item['product_id'], $this->getProductGroupCollection()))
			{
				$qty = $qty + $item['qty'];
			}
		}	
		
		return $qty;
	}
	
}