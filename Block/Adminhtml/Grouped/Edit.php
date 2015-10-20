<?php

class Wdc_Cartex_Block_Adminhtml_Grouped_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
		
		$this->_objectId = 'id';
		$this->_blockGroup = 'faq';
		$this->_controller = 'adminhtml_faq';
		
		$this->_updateButton('save', 'label', Mage::helper('faq')->__('Save Item'));
		$this->_updateButton('delete', 'label', Mage::helper('faq')->__('Delete Item'));
	}
	
	public function getHeaderText()
	{
		if( Mage::registry('faq_data') && Mage::registry('faq_data')->getId() ) {
			return Mage::helper('faq')->__("Edit Item '%s'", Mage::registry('faq_data')->getFaqQuestion());
		} else {
			return Mage::helper('faq')->__('Add Item');
		}
	}
}