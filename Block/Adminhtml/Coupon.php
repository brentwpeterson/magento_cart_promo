<?php

class Wdc_Cartex_Block_Adminhtml_Coupon extends Mage_Adminhtml_Block_Widget_Grid_Container
{

	/**
	 * Block constructor
	 */
	public function __construct()
	{
		$this->_controller = 'adminhtml_coupon';
		$this->_blockGroup = 'cartex';
		$this->_headerText = Mage::helper('cartex')->__('Manage Coupon Codes');
		
		//$this->_addButtonLabel = Mage::helper('cartex')->__('Add New Product Promo');		
		parent::__construct();
		
		$this->_updateButton('save', 'label', Mage::helper('cartex')->__('Save Cart Promo'));
		$this->_removeButton('add');
	}

}
