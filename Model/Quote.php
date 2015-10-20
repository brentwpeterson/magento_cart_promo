<?php

class Wdc_Cartex_Model_Quote extends Mage_Sales_Model_Quote
{
	public function addItemProduct(Mage_Catalog_Model_Product $product, $qty=1)
	{		
		$item = $this->getItemByProduct($product);
		if (!$item) {
			$item = Mage::getModel('sales/quote_item');
			$item->setQuote($this);
		}

		/**
		 * We can't modify existing child items
		 */
		if ($item->getId() && $product->getParentProductId()) {
			return $item;
		}

		$item->setOptions($product->getCustomOptions())
			->setProduct($product);

		$this->addItem($item);


		echo 'in the function 30';
		return $item;
	}
}