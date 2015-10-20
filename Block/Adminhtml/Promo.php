<?php

class Wdc_Cartex_Block_Adminhtml_Promo extends Mage_Adminhtml_Block_Widget_Grid_Container
{

	/**
	 * Block constructor
	 */
	public function __construct()
	{
		$this->_controller = 'adminhtml_promo';
		$this->_blockGroup = 'cartex';
		$this->_headerText = Mage::helper('cartex')->__('Manage Product Promos');
		
		//$this->_addButtonLabel = Mage::helper('cartex')->__('Add New Product Promo');		
		parent::__construct();
		
		$this->_removeButton('add');
	}

}
