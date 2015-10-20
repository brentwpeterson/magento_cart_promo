<?php

/**
 * GO TO - REDOING processProducts() NOW 07/31/10
 * moved add to cart to item.php, need to work on $this->checkCartItemExist, then $this->removeItembyProductId
 * checkProductInExceptionCollection() for Configurable products to add to cart
 * $this->getPromoGroupCollection is the same as getProductInsertCollection() Need to combine them
 * need to finish line 250 $cur = Mage::getModel('cartex/item')->checkItemCurrent($this->_productId);
 * redo setbasePromoProductId()
 * protected function isCurrentCoupon() Check and redo if needed
 * 
 * Can't use this method to check cart $cur = Mage::getModel('cartex/item')->checkItemCurrent($this->_productId);
 * 
*/

class Wdc_Cartex_Model_Cart extends Wdc_Cartex_Model_Rules
{	
	protected $_quote;
	protected $_itemId;
	protected $_quoteId;
	protected $_quoteItemId;	
	protected $_productIds;
	protected $_cartItems;
	protected $_redir;
	protected $_items;
	protected $_productId;
	protected $_group;
	protected $_cartTotal;
	protected $_promoproductCount;
	protected $_shippingMethod;
	protected $_entityTypeId;
	protected $_valueCollection;
	protected $_type;
	protected $_total_item_count;
	protected $_cartItemsCount;
	protected $_promo_add_cnt;
	protected $_groupCollection;
	protected $_productUrl;
	protected $_itemLimit;
	protected $_qualifyItemCount;
	protected $_promoType;
	protected $_positionId;
	
	/**Changes to Cartex **/

	protected $_promoGroupId;
	protected $_currentItemCollection;
	protected $_completeExceptedItemCollection;
	protected $_linkedProductCollection;
	protected $_isLinked;
	protected $_exceptType;
	protected $_couponOnly;
	protected $_discount_itemid;
	protected $_discount_amount;
	protected $_discount;
	protected $_ruleId;
	protected $_couponCode;
	protected $_oneToOne = false;
	
	/** from Mage_CatalogInventory_Model_Observer */
	protected $_checkedProductsQty = array();
	
	/**add for Free gift **/
	protected $_isfreeGift = false;
	
	public function __construct()
	{
		$this->_quoteId = Mage::getSingleton('checkout/session')->getQuoteId();
		$this->_items = Mage::getresourceModel('cartex/cart_item')->getCurrentCart();
		$this->_cartTotal = Mage::getModel('checkout/session')->getQuote()->getsubtotal();
		$this->_shippingMethod = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getShippingMethod();
		return $this;		
	}
		
	/**
	 * This is method setCurrentPromoCollection
	 *
	 * @param mixed $promos sets variables for the current promo collection
	 * Reset all current collections
	 * @return array returns the collection
	 *
	 * Check to see if I use $this->_currentItemCollection??
	 */
	protected function setCurrentPromoCollection($promos)
	{			
		$this->_promoproductCount = 1;		
		$this->_type = 0;
		$this->_promo_add_cnt = 1;	
		$this->_entityTypeId = 0;		
		$this->_promoGroupId = $promos->getId();
		Mage::getSingleton('core/session')->setPromo($promos);		
		$this->_ruleId = $promos->getRuleId();
		$this->_itemLimit = $promos->getItemLimit();
		$this->_promoType = $promos->getPromoType();
		$this->_exceptType = $promos->getExceptionTypeId();
		$this->_discount_amount = $promos->getDiscountAmount();
		if($this->_exceptType == 0){
			$this->_entityTypeId = Mage::getModel('eav/entity_type')->loadByCode('catalog_product')->getId();
		}
		elseif($this->_exceptType == 1){
			$this->_entityTypeId = Mage::getModel('eav/entity_type')->loadByCode('catalog_category')->getId();
		}
		
		if($promos->getUseRules() == 1){
			$this->_oneToOne = true;
		}
		
		$this->setCurrentCollections();
		$this->_currentPromoCollection = $promos;
		
		return $this->_currentItemCollection;	
	}
	
	
	/**
	 * This is method setCurrentCollections
	 *
	 * @return mixed Sets (or resets) all current collections
	 *
	 */

	protected function setCurrentCollections()
	{
		/** TO DO **/
	}
	
	protected function setCurrentRuleId()
	{		
		$couponCollection =  Mage::getresourceModel('cartex/cart_coupon_collection')
			->addFilter('cartex_id', $this->_promoGroupId);
		
		foreach ($couponCollection as $coupon)
		{
			if($this->_couponCode == $coupon->getCouponCode()){
				$this->_ruleId = $coupon->getRuleId();
				break;
			}				
		}
		return $this->_ruleId;
	}
	
	protected function checkConfigType()
	{	
		$val = true;
		$this->_type = 0;	
		$product = Mage::getModel('catalog/product')->load($this->_productId);
		if($product->getTypeId() === 'configurable')
		{
			$val = false;
			$this->_productUrl = $product->getUrlPath();
			$this->_type = 1;	
			$this->setConfigurableLinkedProductCollection();		
		}		
		return $val;
	}
	
	protected function setConfigurableLinkedProductCollection()
	{
		$this->_linkedProductCollection = array($this->_productId);
		$linkProducts = Mage::getResourceModel('catalog/product_type_configurable')->getChildrenIds($this->_productId);	
		foreach ($linkProducts[0] as $id)
		{
			$this->_linkedProductCollection[] = $id;
			$this->_isLinked = true;	
		}		
		return $this->_linkedProductCollection;
	}
	
	protected function _getSession()
	{
		return Mage::getSingleton('checkout/session');
	}
	
	
	protected function setValueCollection()
	{
//		if(!isset($this->_valueCollection) || empty($this->_valueCollection))
//		{
			if(isset($this->_promoGroupId)){		
				$this->_valueCollection = Mage::getresourceModel('cartex/cart_value')->fetchbyPromoGroupId($this->_promoGroupId);
				return $this->_valueCollection;
			}
//		}
//		else
//		{
//			return $this->_valueCollection;
//		}
	}
	
	protected function checkCartRule($code)
	{		
		$val = false;
		$now = Mage::getModel('core/date')->date('Y-m-d');		
		$collection = Mage::getModel('salesrule/rule')->load($code, 'coupon_code');
		
		if($collection->getId() > 0)
		{			
			if($collection->getFromDate() <= $now){
				
				if(strlen($collection->getToDate()) < 4)
				{
					$val = true;
				}
				else
				{
					if($collection->getToDate() > $now)
					{
						$val = true;	
					}
					else
					{
						$val = false;	
					}
				}				
			}
		}	
		
		return $val;
	}
	
	protected function getEntitybyValue($value)
	{
		$productId = 0;
		$collection = Mage::getresourceModel('cartex/cart_value')->fetchEntitybyValue($value);
		if($collection)
		{
			foreach ($collection as $item)
			{
				$productId = $item['wdc_exception_item_id'];	
			}	
		}
		return $productId;		
	}	
	
	public function checkCartValue()
	{
		$items = $this->pricerangeCheck();
		if($items != 0)
		{
			$this->processProducts($this->getEntitybyValue($items));				
		}	
	}
	
	protected function setbasePromoProductId()
	{
		if(isset($this->_productId))
		{
			return (int)$this->_productId;	
		}
		else
		{
			if(isset($this->_promoGroupId)){
				$products = $this->getProductInsertCollection();	
				if($products)
				{				 	
					$this->_promoproductCount = count($products);
					foreach ($products as $productId)
					{
						$this->_productId = $productId;
						break;
					}
				}				
			}
			else
			{
				$this->_productId = 0;
			}
		}	
		//echo $this->_productId;
		return (int)$this->_productId;
	}
	
//	public function getQuoteOrderId($item_id)
//	{
//		$sql ="SELECT quote_item_id FROM sales_flat_order_item where item_id =".$item_id;
//		$result = $this->_read->fetchRow($sql);
//		if($result)
//		{
//			$this->_quoteItemId = $result['quote_item_id'];
//		}
//		else
//		{
//			$this->_quoteItemId = 0;		
//		}		
//		return $this->_quoteItemId;
//	}
	
	public function checkCartTotal($totalcheck=40)
	{
		$var = false;
		$total = Mage::getModel('checkout/session')->getQuote()->getBaseSubtotal();	
		if($total >= $totalcheck)
		{
			$var = true;		
		}
		return $var;
	}
	
	protected function checkValidation()
	{
		$val = true;
		
		if(!isset($this->_type)){
			$this->_type = 0;				
		}		
		/** 070910 Validate EXEX not working? **/
		//$val = $this->validateExException();
		//$val = $this->checkConfigType();
		$val = $this->validateQty();
		
		
		if($this->_couponOnly){
			
			/**Need to work on this 081010**/
			$val = $this->isCurrentCoupon();
		}
			
		//Need to redo ITEM CURRENT!!
		
//		$cur = Mage::getModel('cartex/item')->checkItemCurrent($this->_productId);
//		
//		if($cur){
//			
//			//$val = false;
//		}
//		else{
//			//echo '<br><br>test - checkCurrent YES';
//			//$val = true;
//		}
		
		
		return $val;		
	}
	
	protected function getItemIdxCollection()
	{
		
		$idxCollection = array();
		$quoteItemCollection = Mage::getresourceModel('cartex/cart_idx_collection')
			->addFilter('quote_id', $this->_quoteId);	
			
		foreach ($quoteItemCollection as $item){
			$idxCollection[] = $item['item_id'];
		}
			
		return $idxCollection;
	}
	
	
	protected function isCurrentCoupon()
	{
		return $val = true;	
		//$quoteItemCollection = Mage::getresourceModel('cartex/cart_idx_collection')
		//	->addFilter('quote_id', $this->_quoteId);
	}
	
	/**
//	 * This is method setQualifyItemCount
//	 *
//	 * @return mixed This is the return value description
//	 *	 
//	*/
	protected function setQualifyItemCount()
	{
		$cnt = 0;
		//Haven't used this
		if($this->_oneToOne){			
			if(isset($this->_promoGroupId))
			{
				foreach ($this->_items as $item)
				{
					if($this->checkGroupbyExceptionType($item['product_id'])){
						if(!in_array($item['product_id'], $this->getProductInsertCollection())){
							$cnt = $cnt + $item['qty'];
						}				
					}
				}
			}
		}	
		
		if($cnt == 0)
		{
			$cnt = 1;	
		}
		
		return $cnt;
	}
	
	protected function validateCartQty($productId)
	{
		
		$val = true;
		/* Turn this on if you don't have to worry about multiple promos */
		foreach ($this->_items as $item)
		{
			if($item['product_id'] == $productId){
				$val = false;
				break;
			}				
		}	
		
	/** Check IDX table to go here **/
	
		return $val;				
	}
		
	protected function processProducts()
	{			
		
		//if($this->_productId != 0)
		//{	
			if($this->checkValidation())
			{
									
				$this->setQualifyItemCount();						
				foreach ($this->getProductInsertCollection() as $productId){
					
			
					$this->_productId = $productId;		
			//	echo $productId;				
					if(!$this->checkCartItemExist($productId))
					{			
						try{
							
							$this->addLineCartItem($productId);	
							
						}
						catch(exception $e)
						{						
							if($this->_positionId == 1){
								$this->_getSession()->addError(Mage::helper('cartex')->__('There was a problem - '.$e->getMessage()));
							}					
							Mage::app()->getResponse()->setRedirect('/'.$this->_productUrl);
						}
					}
				}
			}
			else
			{
				if($this->checkCartItemExist($this->_productId))
				{
					$this->removeItembyProductId($this->_productId, 'debug=process->checkCartItemExist()');
				}
				else{						
					if($this->_type == 1 && $this->checkProductInExceptionCollection()){
						$this->_getSession()->addError(Mage::helper('cartex')->__('Did not PassValidation, Please configure your product'));					
						Mage::app()->getResponse()->setRedirect('/'.$this->_productUrl);
					}
				}
			}
		//}
	}
	
//	protected function processMultipleProducts()
//	{
//		$products = $this->getPromoGroupCollection($this->_promoGroupId);				
//		if($products)
//		{				 	
//			foreach ($products as $productId)
//			{
//				$productId = (int)$productId;
//				$this->processProducts($productId);				
//			}		
//		}	
//	}
	
	public function checkShipping($code)
	{
		$var = false;			
		if(!empty($this->_shippingMethod))
		{
			if($this->_shippingMethod == $code)
			{
				$var = true;	
			}
		}		
		return $var;
	}
	
	protected function _getStore()
	{
		return Mage::app()->getStore()->getId();
	}
	
	public function getActivePromoGroups()
	{		
		$this->_groupCollection = Mage::getresourceModel('cartex/cart_entity_collection')
			->addFieldToFilter('store_id', array('in' => array(0, $this->_getStore()),))
		->addFieldToFilter('is_active', array('eq' => array(1),));	
		return $this->_groupCollection;
	}
	
	

	
	protected function getAttributeSetId($productId)
	{
		$product = Mage::getModel('catalog/product')->load($productId);
		return $product->getAttributeSetId();		
	}
	
	public function updateItembyProductId($productId, $qty)
	{		
		$cart = Mage::getModel('checkout/cart');		
		foreach ($this->_items as $item) {
			if ($item['product_id'] == $productId) {
				$itemId = $item['item_id'];				
				$data[$itemId] = array('qty'=>$qty);
				$cart->updateItems($data);
				$cart->save();										
			}
		}
	}
	
	protected function checkGC($certId=0)
	{
		$i = 0;
		$val = true;
		foreach ($this->_items as $item)
		{
			if($i > 1)
			{
				$val = true;
				break;	
			}
			if($item['product_id'] == $certId)
			{
				$val = false;
			}
			else
			{
				$i = $i + $item['qty'];	
			}
			
		}	
		return $val;
	}
		
	protected function setPromoProductCount()
	{		
		$this->_promo_add_cnt = $this->setQualifyItemCount();
		return $this->_promo_add_cnt;
	}
	
	public function checkCartItemExist($productId)
	{		
		$this->setQualifyItemCount();
	$val = false;
	//$val = true;		

			foreach ($this->_items as $item) {						
				if ($item['product_id'] == $productId) {						
					if(Mage::getModel('cartex/item')->checkItemCurrent($productId) == true){		
						if($item['qty'] != $this->_promo_add_cnt){
							$this->updateItembyProductId($productId, $this->_promo_add_cnt);
						}	
						$val = true;
					}
					else{
						$cart = Mage::getModel('checkout/cart');					
						$cart->removeItem($item['item_id'])->save();
						//$val = true;
					}			
					
					break;				
				}
			}	
		
		return $val;
	}
	
	public function removeItembyProductId($productId, $debug='')
	{
		////Mage::helper('errorlog')->insert('removebyproduct', 'productid->'.$productId.' debug='.$debug);		
		$cart = Mage::getModel('checkout/cart');
		foreach ($this->_items as $item) {
			if ($item['product_id'] == $productId) {
								
				$itemId = $item['item_id'];
				
				$cart->removeItem($itemId)->save();				
				Mage::getresourceModel('cartex/cart_idx')->deletebyItemId($item['item_id'], 1, 'removeProduct');
								
			}
		}
	}
	
	public function removeItembyItemId($itemId, $debug='')
	{
		//$debug.= 'groupid->'.$this->_promoGroupId;
		////Mage::helper('errorlog')->insert('removeItembyItemId', '$itemId->'.$itemId.' debug='.$debug);		
	
		$cart = Mage::getModel('checkout/cart');
		$cart->removeItem($itemId)->save();				
		Mage::getresourceModel('cartex/cart_idx')->deletebyItemId($itemId, 1, 'removeItembyItemId');	
				
	}
	
//	public function getPromoGroupCollection($promoGroupId)
//	{		
//			$collection = Mage::getresourceModel('cartex/cart_item')->fetchListbyAttrib($promoGroupId);
//			if($collection)
//			{
//				foreach ($collection as $ids)
//				{
//					$products[]= $ids['id'];	
//				}
//				$this->_productIds = $products;
//				return $this->_productIds;	
//			}
//	
//	}
	
	protected function checkProductInExceptionCollection()
	{
		$val = true;
		foreach ($this->_items as $item)
		{
			if(in_array($item['product_id'], $this->_currentItemCollection))
			{
				$val = false;
			}		
		}	
		
		return $val;
	}
	
	public function getExceptionValue($productId, $promoGroupIds=false)
	{		
		$value = 0;
		if($promoGroupIds)
		{
			//To be used for Hi and low values
			return $value;
		}
		else{
			
			$collection = Mage::getresourceModel('cartex/cart_value')->fetchbyProductId($productId);
			if($collection)
			{
				foreach ($collection as $values)
				{
					$value = $values['value'];	
					break;
				}	
			}		
			return $value;	
		}
	}
	
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
	
	public function getCompleteProductInsertCollection()
	{
		$iarray = array();
		$insertCollection = Mage::getresourceModel('cartex/cart_item_collection');	
		foreach ($insertCollection as $item)
		{
			$iarray[] = $item->getEntityId();
		}
		return $iarray;
	}
	
//	public function getProductInsertCollection()  CHANGED 11/29/10
//	{			
//		$i = 0;		
//		$this->_currentItemCollection = array();
//		$itemCollection = Mage::getresourceModel('cartex/cart_item_collection')
//			->addFilter('wdc_attribute_id', $this->_promoGroupId);	
//		
//		foreach ($itemCollection as $item)
//		{
//			if($this->checkProductSale($item->getEntityId())){
//				$this->_currentItemCollection[] = $item->getEntityId();
//				$i++;
//			}
//			else
//			{
//				/** Set promo to disabled **/
//			}
//										
//		}			
//		if($this->_type == 1)
//		{
//			foreach ($this->setConfigurableLinkedProductCollection() as $eitem)
//			{
//				$this->_currentItemCollection[] = $eitem;
//			}
//		}			
//		if($i == 1)
//		{
//			$this->_productId = $item->getEntityId();		
//		}
//						
//		return $this->_currentItemCollection;
//	}
	
	public function getProductInsertCollection()
	{			
		$i = 0;		
		$this->_currentItemCollection = array();
		$itemCollection = Mage::getresourceModel('cartex/cart_item_collection')
			->addFilter('wdc_attribute_id', $this->_promoGroupId);	
		
		foreach ($itemCollection as $item)
		{
			if($this->checkProductSale($item->getEntityId())){
				$this->_currentItemCollection[] = $item->getEntityId();
				//Last ProductId will become the default ProductId
				$this->_productId = $item->getEntityId();
				$i++;
			}
			else
			{
				/**TO DO! Set promo to disabled **/
			}
			
		}		
			
//		if($this->_type == 1)
//		{
//			foreach ($this->setConfigurableLinkedProductCollection() as $eitem)
//			{
//				$this->_currentItemCollection[] = $eitem;
//			}
//		}	
		
		return $this->_currentItemCollection;
	}
	
	protected function getCurrentCartProductCollection()
	{
		$cartProducts = array();
			
		foreach ($this->_items as $item)
		{			
			switch($this->_exceptType)
			{
				case 0:
					$cartProducts[] = $item['product_id'];
					break;
				case 1:
					foreach ($this->_getProductCategories($item['product_id']) as $catId)
					{
						$cartProducts[] = $catId;
					}										
					break;
				case 2:
					$product = Mage::getModel('catalog/product')->load($item['product_id']);
					$cartProducts[] = $product->getAttributeSetId();
					break;					
				default:
					$cartProducts[] = $item['product_id'];
					break;
										
			}
			
			////Mage::helper('errorlog')->insert('getCurrentCartProductCollection()', $product->getAttributeSetId());
		}		
		return $cartProducts;
	}
	
	protected function _getProductCategories($productId)
	{
		if(Mage::getVersion() < 1.4){
			$product = Mage::getModel('catalog/product')->load($productId);
			return $product->getCategoryIds();
		}
		else{
			
			$catIds = array();
			
			$product = Mage::getModel('catalog/product')->load($productId);			
			$collection = $product->getCategoryCollection();
			
			foreach ($collection as $category){
				
				$catIds[] = $category->getId();
			}
			
			return $catIds;
		}
		
	}
	
	
	protected function getCurrentCartItemCollection()
	{
		$cartItems = array();
		
		foreach ($this->_items as $item)
		{			
			$cartItems[] = $item['item_id'];
		}		
		return $cartItems;
	}
	
	public function checkGroupbyExceptionType($productId)
	{
		$val = false;
	//	//Mage::helper('errorlog')->insert('checkgroup', 'except->'.$this->_exceptType.' productid->'.$productId);
		if($this->_exceptType == 0){
			if(in_array($productId, $this->getProductGroupCollection()) || in_array($productId, $this->getProductInsertCollection()))
			{										
				$val = true;
			} 
		}
		elseif($this->_exceptType == 1){
			$match = array_intersect($this->getProductGroupCollection(), $this->_getProductCategories($productId));			
			if(count($match) > 0 || in_array($productId, $this->getProductInsertCollection()))
			{
				$val = true;
			}
		}
		elseif($this->_exceptType == 2){	
			$product = Mage::getModel('catalog/product')->load($productId);
			if(in_array($product->getAttributeSetId(), $this->getProductGroupCollection()) || in_array($productId, $this->getProductInsertCollection()))
			{
				$val = true;
			} 
		}
		
		
		return $val;
	}
	
	
	/**
//	 * Count items in the cart that are in the Exception Group
//	 *
//	 * @return int returns the total number of items in the cart from Exception Groups
//	 *
//	 */
	protected function countItemsinGroup()
	{
		$this->_qualifyItemCount = 0;
		foreach ($this->_items as $item)
		{
			if($this->checkGroupbyExceptionType((int)$item['product_id'])){				
				$this->_qualifyItemCount++;				 
			}			
		}
		
		return $this->_qualifyItemCount;
	}		
	
	
	
	
	/**
//	 * check to see if item is in the rest of the exception collection
//	 *
//	 * @return bool Return true if exists
//	 *
//	 */
	protected function validateExException()
	{
		$val = false;
		if(in_array($this->_productId, $this->getCompleteExceptionCollection())){
			$val = true;
		}
		//return $val;
		return true;  	
	}
	
	
	/**
//	 * Validates the Quantity of cart with Exception Limit 
//	 *
//	 * @return bool Returns true or false
//	 *
//	 */
	protected function validateQty()
	{
		$val = false;			
		if($this->_promoType == 0 || $this->_promoType == 2){
			if($this->_cartItemsCount >= $this->_itemLimit)
			{
				$val = true;
			}
			
		}
		else{
			if($this->countItemsinGroup() >= $this->_itemLimit)
			{
				$val = true;
			}
		}
		
		return $val;	
	}
	
	protected function removePromoIds()
	{
		foreach ($this->_items as $item) {	
			if(in_array($item['product_id'], $this->_currentItemCollection))
			{
				$this->removeItembyProductId($item['product_id'], 'debug=removePromoIds()');
			}
		}	
	}
	
	protected function getCompleteExceptionCollection()
	{
		if(isset($this->_completeExceptedItemCollection) && !empty($this->_completeExceptedItemCollection)){
			return $this->_completeExceptedItemCollection;			
		}
		else{			
			$itemCollection = Mage::getresourceModel('cartex/cart_item')->fetchExceptionbyAttributeId($this->_promoGroupId);		
			foreach ($itemCollection as $item)
			{
				$this->_completeExceptedItemCollection[] = $item;										
			}			
			return $this->_completeExceptedItemCollection;
		}	
	}
	
	public function getProductGroupCollection()
	{			
		$this->_currentGroupCollection = array();
			$groupCollection = Mage::getresourceModel('cartex/cart_groups_collection')
				->addFilter('entity_type_id', $this->_entityTypeId)
				->addFilter('wdc_attribute_id', $this->_promoGroupId);
			
			foreach ($groupCollection as $item)
			{
				if($this->_exceptType == 2){
					
					$this->_currentGroupCollection[] = (int)$item->getAttributeSetId();
				}
				else{
					$this->_currentGroupCollection[] = (int)$item->getEntityId();
				}		
			}		
			return $this->_currentGroupCollection;

	}
	
	protected function _getProduct($productId)
	{		
		return Mage::getModel('catalog/product')->load($productId);				
	}
	
	protected function setProductId($productId)
	{
		$this->_productId = $productId;	
		return $this->_productId;
	}
	
	public function addProductLineItem($productId, $promoId)
	{			
		$this->_productId = $productId;		
		$this->setCurrentPromoCollection(Mage::getModel('cartex/cart_entity')->load($promoId));
		$this->_isfreeGift = true;
		$this->addLineCartItem();
	}
	
	public function addLineCartItem($productId){
				
	$this->_quoteId = Mage::getSingleton('checkout/session')->getQuoteId();
		$product = $this->_getProduct($productId);
		$this->_productId = $productId;
							
		
		
		$sellPrice = $product->getFinalPrice() - $this->_discount_amount;
			
		$product->setPrice($sellPrice);
		$product->setFinalPrice($sellPrice);
		
		$data = array(
			'quote_id'	=>	$this->_quoteId, 
			'created_at'	=>	Mage::getModel('core/date')->date('Y-m-d'),
			'updated_at'	=>	Mage::getModel('core/date')->date('Y-m-d'), 
			'product_id'	=>	$productId,
			'parent_item_id'	=>	NULL, 
			'is_virtual'	=>	'0', 
			'sku'	=>	$product->getSku(),
			'name'	=>	$product->getName(),
			//'description'	=>	NULL, 
			'applied_rule_ids'	=>	$this->setCurrentRuleId(),
			'additional_data'	=>	NULL, 
			'free_shipping'	=>	'0',
			'is_qty_decimal'	=>	'0', 
			'no_discount'	=>	'1', 
			'weight'	=>	$product->getWeight(),
			'qty'	=>	$this->_promo_add_cnt,
			'price'	=>	$sellPrice,
			'base_price'	=>	$sellPrice, 
			'custom_price'	=>	$sellPrice, 
			'discount_percent'	=>	'0.0000',
			'discount_amount'	=>	$this->_discount_amount,
			'base_discount_amount'	=>	$this->_discount_amount, 
			'tax_percent'	=>	'0.0000',
			'tax_amount'	=>	'0.0000',
			'base_tax_amount'	=>	'0.0000', 
			'row_total'	=>	$sellPrice * $this->_promo_add_cnt,
			'base_row_total'	=>	$sellPrice * $this->_promo_add_cnt, 
			'row_total_with_discount'	=>	$sellPrice * $this->_promo_add_cnt,
			'row_weight'	=>	$product->getWeight() * $this->_promo_add_cnt, 
			'product_type'	=>	$product->getTypeId(),
			'base_tax_before_discount'	=>	'0.0000', 
			'tax_before_discount'	=>	'0.0000', 
			'original_custom_price'	=>	$sellPrice, 
			'gift_message_id'	=>	NULL, 
			'weee_tax_applied'	=>	'a:0:{}', 
			'weee_tax_applied_amount'	=>	'0.0000', 
			'weee_tax_applied_row_amount'	=>	'0.0000', 
			'base_weee_tax_applied_amount'	=>	'0.0000', 
			'base_weee_tax_applied_row_amount'	=>	'0.0000', 
			'weee_tax_disposition'	=>	'0.0000',
			'weee_tax_row_disposition'	=>	'0.0000', 
			'base_weee_tax_disposition'	=>	'0.0000', 
			'base_weee_tax_row_disposition'	=>	'0.0000',
						
			);
				
			if($this->checkVersion() == 2){
			
			$v14 =array('store_id' => $this->_getStore());
			$data = array_merge($data, $v14);
			
			}	
			
		
			Mage::getresourceModel('cartex/cart_idx')->insertCartItem($data, $this->_promoGroupId);			
						
	}
	
	
	/**
	//	 * Main function to check cart
	//	 * need to modify $group function
	//	 * */
	
	//	public function checkCart($type=0)
	//	{
	//		$groups = Mage::getresourceModel('cartex/cart_entity')->fetchActiveGroups();
	//		
	//		if($groups){
	//			foreach ($groups as $groupId){
	//				$this->_type = $type;			
	//				$this->_promoGroupId = $groupId;
	//				
	//				/** Check Price range for Cart functions**/
	//				//	$this->checkPriceRange();		
	//				
	//				/** Get the product id for base exception (product to add) **/
	//				$productId = $this->setbasePromoProductId();
	//				
	//				
	//				if(!isset($_SESSION['promobottle']))
	//				{
	//					$_SESSION['promobottle'] = false;
	//				}
	//				
	//				if($_SESSION['promobottle'] == false)
	//				{				
	//					
	//					/** promo product count is the amount of products to add to the cart**/
	//					if($this->_promoproductCount == 1)
	//					{
	//						
	//						$this->processProducts();	
	//					}
	//					else
	//					{
	//						$this->processMultipleProducts();
	//					}
	//					
	//				}	
	//				else
	//				{			
	//					
	//					if($this->checkCartItemExist($productId))
	//					{
	//						$this->removeItembyProductId($productId, 'debug=checkCart->checkCartItemExist()');
	//					}
	//				}
	//			}
	//		}							
	//	}	
	
	//	protected function checkValue($n)
	//	{
	//		if($this->checkGC()){
	//			$this->setValueCollection();		
	//			foreach ($this->_valueCollection as $items)
	//			{				
	//				if($items['wdc_exception_item_id'] == $n)
	//				{
	//					$this->_productId = $n;
	//					$this->processProducts();
	//				}
	//				else
	//				{
	//					if($this->checkCartItemExist($items['wdc_exception_item_id']))
	//					{
	//						$this->removeItembyProductId($items['wdc_exception_item_id'], 'checkvalue!');
	//					}
	//				}
	//			}
	//		}	
	//	}
	
}