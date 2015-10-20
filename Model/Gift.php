<?php

class Wdc_Cartex_Model_Gift extends Wdc_Cartex_Model_Cartex
{
	public function checkCart()
	{
		$this->_sideAlert = false;
		foreach ($this->getActivePromoGroups() as $promos)
		{
			$this->setCurrentPromoCollection($promos);
			if($this->_promoType == 11)
			{
				foreach ($this->_getCurrentCartProductIds() as $productId)
				{
					$val = $this->checkGroupbyExceptionType($productId);
					if($val)
					{
						//$this->setPromoId($promos->getId());
						//$this->setPromoId(1234);
						$this->_sideAlert = $promos->getId();
						break;	
					}
				}
			}
		}		
		return $this->_sideAlert;
	}
	
	public function getPromoId()
	{		
		return $this->_promoGroupId;
	}
	
	protected function _getCurrentCartProductIds()
	{
		$cartProducts = array();
		foreach ($this->_items as $item){
			$cartProducts[] = $item['product_id'];
		}
		return $cartProducts;
	}
	
	protected function setPromoId($promoId)
	{
		$this->_promoGroupId = $promoId;
		return $this->_productId;	
	}
	
	
	
	
}