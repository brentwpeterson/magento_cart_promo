<?php

/**
 * Thing to do 
 * checkItemCurrent need new method changed in
 *	cart.php 504  checkCartItemExist($productId)
 * checkValidation() 241 
 * */

class Wdc_Cartex_Model_Item extends Wdc_Cartex_Model_Cartex 
{
	protected $_itemEx;
	protected $_items;
	protected $_quoteId;
	protected $_itemIdxCollection;
	protected $_itemId;
	
	
	public function __construct()
	{
		//$this->_items = Mage::getModel('checkout/session')->getQuote()->getAllItems();
		$this->_quoteId = Mage::getModel('checkout/session')->getQuoteId();
		$this->_itemEx = false;
	}
	
	public function checkItemCurrent($productId)
	{		
		$val = false;	
		//$val = true;		
		$items = Mage::getresourceModel('cartex/cart_idx')->fetchbyProductQuoteId($productId, $this->_quoteId);
		if($items){
			foreach ($items as $item){
				if($item['is_current'] == 1){	
					$this->_itemId = $item['item_id'];					
					$val = true;			
				}			
			}
		}	
		return $val;
	}
	
	public function drawLine($productId)
	{		
		if($this->checkItemCurrent($productId)){			
			return '<a href="/cartex/post/remove/item_id/'.$this->_itemId.'/">Click to remove your promo item</a>';
		}	
	}
	
	public function checkInsertProduct($productId)
	{
		$val = false;
		$items = Mage::getresourceModel('cartex/cart_idx')->fetchbyProductQuoteId($productId, $this->_quoteId);	
		if($items){
			$val = true;
		}
		return $val;
	}
	
	public function checkProductExist($productId, $cartexId, $qty=1){
		
		$this->_itemEx = Mage::getresourceModel('cartex/cart_idx')
			->checkProductExist($productId, $this->_quoteId, $cartexId, $qty);	
	}
	
	public function cartItemIdxExist($productId, $discount=false, $qty=1)
	{		
		$items = Mage::getresourceModel('cartex/cart_item')->getCurrentCart();
		if($this->_itemEx == false){			
			if(isset($items) && !empty($items)){
				$i=0;
				foreach($items as $item){	
					
					if($item['product_id'] == $productId){												
						$this->_itemEx = Mage::getresourceModel('cartex/cart_idx')
							->checkItemExist($item['item_id'], $productId, $discount, $this->_quoteId, $item->getQty());
						//						if($this->_itemEx === true){
						//							$cart = Mage::getModel('checkout/cart');
						//							$cart->addProduct($productId, $qty);
						//							$cart->save();
						//						}
						$i++;	
					}
				}
			}
		}		
	}
	
	
	
	public function getIdxCollection()
	{		
		$itemCollection = Mage::getresourceModel('cartex/cart_idx')->fetchbyQuoteId($this->_quoteId);		
		foreach ($itemCollection as $item)
		{
			$this->_itemIdxCollection[] = $item['item_id'];										
		}			
		return $this->_itemIdxCollection;
	}
	

	
}