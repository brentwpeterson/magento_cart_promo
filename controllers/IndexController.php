<?php

class Wdc_Cartex_IndexController extends Mage_Core_Controller_Front_Action
{
	//date('Y-m-d_H-i-s')
	//protected $_currentItemCollection;
	//protected $_rulePrices = array();
	//protected $_currentPromoCollection;
	//protected $_productId;
	
	public function indexAction()
	{
		$this->_redirect('checkout/onepage', array('_secure'=>true));
	}
	
	public function testAction()
	{
		$promo = Mage::getModel('cartex/cart_entity')->load(3);
		echo Mage::getModel('cartex/rules_buyxgety')->processRule($promo);
	}
	
	public function ztestAction()
	{
		$xmldata = "
		<DATA>
	<ROW>
		<ENTITY_ID>1</ENTITY_ID>
		<CODE>9780967202617</CODE>
	</ROW>
	<ROW>
		<ENTITY_ID>10</ENTITY_ID>
		<CODE>APC70</CODE>
	</ROW>
	<ROW>
		<ENTITY_ID>100</ENTITY_ID>
		<CODE>752289790508</CODE>
	</ROW>
	<ROW>
		<ENTITY_ID>1000</ENTITY_ID>
		<CODE>811922000012</CODE>
	</ROW>
	<ROW>
		<ENTITY_ID>1001</ENTITY_ID>
		<CODE>811922000029</CODE>
	</ROW>
	<ROW>
		<ENTITY_ID>1002</ENTITY_ID>
		<CODE>811922001095</CODE>
	</ROW>
	<ROW>
		<ENTITY_ID>1003</ENTITY_ID>
		<CODE>811922000128</CODE>
	</ROW>
	<ROW>
		<ENTITY_ID>1004</ENTITY_ID>
		<CODE>811922000111</CODE>
	</ROW>
	<ROW>
		<ENTITY_ID>1005</ENTITY_ID>
		<CODE>811922001101</CODE>
	</ROW>
	<ROW>
		<ENTITY_ID>1006</ENTITY_ID>
		<CODE>811922000241</CODE>
	</ROW>
</DATA>
";

echo $xmldata;
	}
	
	protected function setCurrectPromoCollection($promos)
	{
		return $this->_currentPromoCollection = $promos;	
	}
	
	public function setCartFunctions()
	{
		$collection = Mage::getModel('cartex/cart')->getActivePromoGroups();
		
		
		if($collection)
		{
			foreach ($collection as $promos)
			{
				echo $promos->getId();
				$col =  Mage::getResourceModel('cartex/cart_value_collection')
					->addFilter('wdc_attribute_id', $promos->getId());
				//$this->setCurrectPromoCollection($promos);
				//$this->processRequests();	
				echo 'test';
				foreach ($col as $c)
					{
					echo 'SUPER<br>';	
					}
				
			}
		}	
	}
	
	protected function processRequests()
	{
		switch($this->_currentPromoCollection->getPromoType())
		{
			case 0:
				//$this->checkPriceRange();
				echo 'made it to 0';
				break;
			case 1:
				//$this->checkGroup();
				break;
			default:				
				break;				
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
	
	public function getProductInsertCollection()
	{			
		
		$i = 0;		
		$this->_currentItemCollection = array();
		$itemCollection = Mage::getresourceModel('cartex/cart_item_collection')
			->addFilter('wdc_attribute_id', 3);	
		
		foreach ($itemCollection as $item)
		{
			if($this->checkProductSale($item->getEntityId())){
				$this->_currentItemCollection[] = $item->getEntityId();
				$i++;
			}
			else
			{
				/** Set promo to disabled **/
			}
			
		}			
		
		if($i == 1)
		{
			$this->_productId = $item->getEntityId();		
		}			
		
		return $this->_currentItemCollection;
	}

	public function setCustomOption($productId, $title, array $optionData, array $values = array())
	{
		//Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
		if (!$product = Mage::getModel('catalog/product')->load($productId)) {
			throw new Exception('Can not find product: ' . $productId);
		}
		
		$defaultData = array(
			'type'			=> 'field',
			'is_require'	=> 0,
			'price'			=> 0,
			'price_type'	=> 'fixed',
			);
		
		$data = array_merge($defaultData, $optionData, array(
			'product_id' 	=> (int)$productId,
			'title'			=> $title,
			'values'		=> $values,
			));
		
		$product->setHasOptions(1)->save();										
		$option = Mage::getModel('catalog/product_option')->setData($data)->setProduct($product)->save();
		
		return $option;
	}
	
	protected function getCatCollection()	{
		
		$storeId = $this->getRequest()->getParam('store', $this->_getDefaultStoreId());
		
		$collection = Mage::getModel('catalog/category')->getCollection();
			$collection->addAttributeToSelect('name')
				->addAttributeToSelect('level')
				->addAttributeToSelect('is_active')
				//->setProductStoreId($storeId)			
				->setStoreId($storeId);

		
		return $collection;
	}
	
	protected function _getDefaultStoreId()
	{
		return Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
	}
	
	public function addAction()
	{
		
		$cart = Mage::getModel('checkout/cart');
		$id = $this->getRequest()->getParam('promoid');	
		
		$groupedproductCollection = Mage::getresourceModel('cartex/cart_products_collection')
			->addFilter('wdc_attribute_id', $id);	
		foreach ($groupedproductCollection as $item)
		{	
			$_product = Mage::getModel('catalog/product')->load((int)$item->getEntityId());
			try{
			if($_product->getIsInStock()){
				$cart->addProduct((int)$item->getEntityId(), 1);
				$cart->save();
			}
			}
			catch(exception $e)
			{
				$this->_getSession()->addError(Mage::helper('cartex')->__($e->getMessage()));
				$this->_redirect('checkout/cart/');
			}
							
		}
		$promo = Mage::getModel('cartex/cart_entity')->load($id);
		$rule = Mage::getModel('salesrule/rule')->load($promo->getRuleId());
		$quote = Mage::getModel('checkout/session')->getQuote();
		
		//echo $rule->getCouponCode();
		
		$quote->setCouponCode($rule->getCouponCode()); //
		$quote->save();
		
		$this->_redirect('checkout/cart/');
	}
	
	protected function _getSession()
	{
		return Mage::getSingleton('checkout/session');
	}
	
	
		

	
}

?>