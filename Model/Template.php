<?php

class Wdc_Cartex_Model_Template extends Mage_Core_Model_Abstract
{
	protected $_write;
	protected $_read;
	protected $_categoryId;
	protected $_level;
	
	public function __construct()
	{
		$this->_write = Mage::getSingleton('core/resource')->getConnection('core_write');		
		$this->_read = Mage::getSingleton('core/resource')->getConnection('core_read');	
		$this->_categoryId = Mage::getSingleton('catalog/layer')->getCurrentCategory()->getId();
		$this->_level = Mage::getSingleton('catalog/layer')->getCurrentCategory()->getLevel();
		$this->_init('cartex/template');	
	}
}