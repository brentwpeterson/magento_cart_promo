<?php

class Wdc_Cartex_Model_Mysql4_Cart_Idx extends Mage_Core_Model_Mysql4_Abstract
{
	protected function _construct()
	{
		$this->_init('cartex/cart_idx', 'item_idx_id');
		
	}	
	
	public function fetchbyItemProductId($itemId, $productId)
	{
		$sql = $this->_getReadAdapter()->select()
			->from($this->getMainTable(), array('item_idx_id'))		
			->where('product_id=?', $productId)
			->where('item_id=?', $itemId);
		
		return $this->_getReadAdapter()->fetchCol($sql);		
	}
	
	public function fetchbyItemQuoteId($itemId, $quoteId)
	{
		$sql = $this->_getReadAdapter()->select()
			->from($this->getMainTable(), array('*'))		
			->where('quote_id=?', $quoteId)
			->where('item_id=?', $itemId);
		
		return $this->_getReadAdapter()->fetchAll($sql);		
	}
	
	public function fetchRowbyItemId($itemId)
	{
		$sql = $this->_getReadAdapter()->select()
			->from($this->getMainTable(), array('*'))			
			->where('item_id=?', $itemId);		
		return $this->_getReadAdapter()->fetchRow($sql);		
	}
	
	public function fetchbyProductQuoteId($productId, $quoteId)
	{
		$sql = $this->_getReadAdapter()->select()
			->from($this->getMainTable(), array('*'))		
			->where('quote_id=?', $quoteId)
			->where('product_id=?', $productId);
		
		return $this->_getReadAdapter()->fetchAll($sql);		
	}
	
	public function fetchbyCartexIdProductQuoteId($productId, $quoteId, $cartexId)
	{
		$sql = $this->_getReadAdapter()->select()
			->from($this->getMainTable(), array('*'))		
			->where('quote_id=?', $quoteId)
			->where('product_id=?', $productId)
			->where('cartex_id=?', $cartexId);
		
		return $this->_getReadAdapter()->fetchAll($sql);		
	}
	
	public function fetchbyQuoteId($quoteId)
	{
		$sql = $this->_getReadAdapter()->select()
			->from($this->getMainTable(), array('*'))		
			->where('quote_id=?', $quoteId);		
		
		return $this->_getReadAdapter()->fetchAll($sql);		
	}
	
	public function getQuoteItemOptionId($itemId)
	{		
		$sql = $this->_getReadAdapter()->select()
			->from($this->getTable('sales/quote_item_option'), array('option_id'))		
			->where('item_id=?', $itemId);		
		return $this->_getReadAdapter()->fetchRow($sql);		
	}
	
	public function getCheckQuoteItemOptionId($itemId, $productId)
	{		
		$sql = $this->_getReadAdapter()->select()
			->from($this->getTable('sales/quote_item_option'), array('option_id'))		
			->where('item_id=?', $itemId)
			->where('code=?', 'product_qty_'.$productId)	
			->where('product_id=?', $productId);	
		return $this->_getReadAdapter()->fetchRow($sql);		
	}
	
	public function getCheckProductItemOptionId($itemId, $productId)
	{		
		$sql = $this->_getReadAdapter()->select()
			->from($this->getTable('sales/quote_item_option'), array('option_id'))		
			->where('item_id=?', $itemId)		
			->where('code=?', 'simple_product')		
			->where('product_id=?', $productId);	
		return $this->_getReadAdapter()->fetchRow($sql);		
	}
	
	public function checkItemExist($itemId, $productId, $discount, $quoteId=0,  $qty=1)
	{		
		$options = $this->getQuoteItemOptionId($itemId);		
		if($options){
			$optionModel = Mage::getModel('sales/quote_item_option')->load($options['option_id']);
		}	
		else{
			$optionModel = Mage::getModel('sales/quote_item_option');
		}
		$optionModel->setItemId($itemId);
		$optionModel->setProductId($productId);
		$optionModel->setCode('product_qty_'.$productId);
		$optionModel->setValue($qty);
		$optionModel->save();	
		
		if($discount){
			$d = 1;
		}		
		else{
			$d = 0;
		}
		
		$val = false;
		$results = $this->fetchbyProductQuoteId($productId, $quoteId);
		if($results){
			
			foreach ($results as $row)
			{
				if($row['is_discount'] == 0){
					$idx = Mage::getModel('cartex/cart_idx')->load($row['item_idx_id']);
					$idx->setItemId($itemId)
						->setProductId($productId)
						->setQuoteId($quoteId)
						->setIsDiscount($d)
						->setQty($qty)
						->save();				
					
				}
				else{
					$idx = Mage::getModel('cartex/cart_idx')->load($row['item_idx_id'])
						->setItemId($itemId)
						->setProductId($productId)
						->setQuoteId($quoteId)
						->setIsDiscount($d)
						->setQty($qty)
						->save();					
				}		
			}
			$val = true;
		}
		else{
			$idx = Mage::getModel('cartex/cart_idx')
				->setItemId($itemId)
				->setProductId($productId)
				->setQuoteId($quoteId)
				->setIsDiscount($d)
				->setQty($qty)
				->save();
			$val = true;
		}
		return $val;
	}
	
	public function deletebyItemId($itemId, $current=1, $debug='')
	{		
		//Mage::helper('errorlog')->insert('deleteItem', 'itemId->'.$itemId.' debug='.$debug);
		$this->_getWriteAdapter()->delete($this->getMainTable(), array("item_id = {$itemId}", "is_current = {$current}"));
	}
	
	public function insertProduct($productId, $itemId, $quoteId, $cartexId, $qty, $itemidxId=0){
		if($itemidxId != 0){			
			
			$idx = Mage::getModel('cartex/cart_idx')->load($itemidxId)
				->setItemId($itemId)
				->setProductId($productId)
				->setQuoteId($quoteId)	
				->setCartexId($cartexId)	
				->setQty($qty)
				->save();	
		}
		else{
			$idx = Mage::getModel('cartex/cart_idx')
				->setItemId($itemId)
				->setProductId($productId)
				->setQuoteId($quoteId)	
				->setCartexId($cartexId)			
				->setQty($qty)
				->save();		
		}
	}
	
	public function checkProductExist($productId, $quoteId=0, $cartexId, $qty=1)
	{		
		$val = false;
		$results = $this->fetchbyProductQuoteId($productId, $quoteId);
		if($results){			
			foreach ($results as $row)
			{
				$this->insertProduct($productId, $row['item_id'], $quoteId, $cartexId, $qty, $row['item_idx_id']);				
			}
			
		}
		else{
			$items = $this->getCartItems();
			foreach ($items as $item){
				if($item['product_id'] == $productId){
					$this->insertProduct($productId, $item['item_id'], $quoteId, $cartexId, $qty);
					break;
				}
			}
		}
		return $val;
	}
	
	protected function getCartItems()
	{
		return Mage::getresourceModel('cartex/cart_item')->getCurrentCart();
	}
	
	protected function checkIDXexist($productId, $quoteId, $cartexId)
	{
		
		$inCart = false;
		$inIdx = false;
		$val = true;		
		/**CHECK IF IN CART **/
		foreach ($this->getCartItems() as $item)
		{
			if($item['product_id'] == $productId)
			{
				$val = false;
				$inCart = true;	
				break;
			}			
		}		
		
		/** CHECK IF IN IDX **/
		$idxset = $this->fetchbyCartexIdProductQuoteId($productId, $quoteId, $cartexId);
		$promo = Mage::getModel('cartex/cart_entity')->load($cartexId);
		if($idxset){
			foreach ($idxset as $idxitem){
				$val = false;
				$inIdx = true;
				break;
			}			
		}
						
		/** if not in cart, and in idx return true **/
		
		if(!$inCart && $inIdx){
			$val = true;
		}
		
		return $val;
	}
	
	public function insertCartItem($data, $cartexId)
	{
		Mage::getModel('cartex/cartex')->cartPrepare(true);
		
		$quoteId = Mage::getModel('checkout/session')->getQuoteId();
		
		//Mage::helper('errorlog')->insert(' insertCartItem', 'product->'.$data['product_id'].' quote->'.$quoteId.' cartex->'.$cartexId);
				
		if($this->checkIDXexist($data['product_id'], $quoteId, $cartexId)){
			$this->_getWriteAdapter()->insert($this->getTable('sales/quote_item'), $data);
		}
		$this->checkProductExist($data['product_id'], $quoteId, $cartexId, $data['qty']);	
		
		$this->insertOrphanItemIds($data['product_id'], $quoteId);
		
		$collection = $this->fetchbyQuoteId($quoteId);
		
		foreach ($collection as $item)
		{			
			//Mage::helper('errorlog')->insert('Inside funciton->', 'cartexid=>'.$item['cartex_id'].' -sell->'.$sellPrice);
			
			if(in_array($data['product_id'], Mage::getModel('cartex/cart')->getCompleteProductInsertCollection())){
				
				$promo = Mage::getModel('cartex/cart_entity')->load($item['cartex_id']);	
				
				$product = Mage::getModel('catalog/product')->load($item['product_id']);
				$quote = Mage::getModel('sales/quote')->load($quoteId);
				
				$price = $product->getFinalPrice();
				$sellPrice = $price - $promo->getDiscountAmount();	
				
				$product->setPrice($sellPrice)->setFinalPrice($sellPrice);
				
				$qItem = Mage::getModel('sales/quote_item')
					->setProduct($product)
					->setQuote($quote)
					->setId($item['item_id'])
					->setPrice($sellPrice)
					//->setBaseDiscountAmount(5)
					->setBasePrice($sellPrice)
					->setOriginalCustomPrice($sellPrice)
					->setRowTotal($sellPrice)
					->setCustomPrice($sellPrice);
				//if($set){	
				$qItem->save();
				//}
			}		
		}
		
		//Mage::getModel('cartex/cartex')->cartPrepare(false);
		//Mage::helper('errorlog')->insert('insertcart', $test->getId());
	}
	
	public function updateCartItem($itemId)
	{
		$data = array(
			'custom_price' => 5.00,
			'no_discount' => '0',
			);
		$condition = $this->_getWriteAdapter()->quoteInto('item_id=?', $itemId);
		$this->_getWriteAdapter()->update($this->getTable('sales/quote_item'), $data, $condition);
		return $this;		
	}
	
	
	public function checkOptionTableItemExist($productId, $quoteId)
	{
		$sql = $this->_getReadAdapter()->select()
			->from($this->getTable('sales/quote_item'), array('item_id'))		
			->where('quote_id=?', $quoteId)		
			->where('product_id=?', $productId);	
		return $this->_getReadAdapter()->fetchAll($sql);
	}
	
	public function checkOptionItemExist($productId, $itemId)
	{
		$val = false;
		$result = $this->getCheckProductItemOptionId($itemId, $productId);	
		if($result){
			$val = true;
		}
		return $val;
	}
	
	public function insertOrphanItemIds($productId, $quoteId)
	{
		$itemCollection = $this->checkOptionTableItemExist($productId, $quoteId);
		
		if($itemCollection){
			foreach ($itemCollection as $item)
			{
				if(!$this->checkOptionItemExist($productId, $item['item_id']))
				{
					$optionModel = Mage::getModel('sales/quote_item_option');
					$optionModel->setItemId($item['item_id']);
					$optionModel->setProductId($productId);
					$optionModel->setCode('info_buyRequest');
					$optionModel->setValue('a:1:{s:3:"qty";i:1;}');
					$optionModel->save();
					
					$optionModel = Mage::getModel('sales/quote_item_option');
					$optionModel->setItemId($item['item_id']);
					$optionModel->setProductId($productId);
					$optionModel->setCode('product_qty_'.$productId);
					$optionModel->setValue('1');
					$optionModel->save();
				}		
			}
		}
	}
	
	public function checkItemExists($itemId)
	{
		 $val = false;
	
		if($this->fetchRowbyItemId($itemId))
		{
			$val = true;
		}
		
		return $val;	
	}
}