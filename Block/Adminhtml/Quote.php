<?php


class Wdc_Cartex_Block_Adminhtml_Quote extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
//		$this->_controller = 'adminhtml_quote';
//		$this->_blockGroup = 'cartex';
//        $this->_headerText = Mage::helper('salesrule')->__('Shopping Cart Price Rules');
//        $this->_addButtonLabel = Mage::helper('salesrule')->__('Add New Rule');
//        parent::__construct();
        
		$this->_controller = 'adminhtml_quote';
		$this->_blockGroup = 'cartex';
		$this->_headerText = Mage::helper('cartex')->__('Manage Promos');
		$this->_addButtonLabel = Mage::helper('cartex')->__('Add New Promo');		
		parent::__construct();
        
    }
}
