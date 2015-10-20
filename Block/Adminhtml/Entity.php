<?php

class Wdc_Cartex_Block_Adminhtml_Entity extends Mage_Adminhtml_Block_Widget_Grid_Container
{

	/**
	 * Block constructor
	 */
	public function __construct()
	{
		$this->_controller = 'adminhtml_entity';
		$this->_blockGroup = 'cartex';
		$this->_headerText = Mage::helper('cartex')->__('Manage Promos');
		$this->_addButtonLabel = Mage::helper('cartex')->__('Add New Promo');		
		parent::__construct();
	}

}
