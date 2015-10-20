<?php

class Wdc_Cartex_Block_Adminhtml_Quote_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

	

	public function __construct()
	{
		parent::__construct();
		$this->setId('quote_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('cartex')->__('Manage Coupon Promos'));
		$couponId = Mage::app()->getFrontController()->getRequest()->get('id');	
	}

	protected function _beforeToHtml()
	{		
		
		$this->addTab('form_section', array(
			'label'     => Mage::helper('cartex')->__('Cart Promo Information'),
			'title'     => Mage::helper('cartex')->__('Cart Promo Information'),
			'content'   => $this->getLayout()->createBlock('cartex/adminhtml_quote_edit_tab_form')->toHtml(),
			));
			return parent::_beforeToHtml();
		}
	
}