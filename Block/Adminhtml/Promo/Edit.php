<?php

class Wdc_Cartex_Block_Adminhtml_Promo_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'cartex';
        $this->_controller = 'adminhtml_promo';

        $this->_updateButton('save', 'label', Mage::helper('cartex')->__('Save Cart Promo'));
        $this->_updateButton('delete', 'label', Mage::helper('cartex')->__('Delete Cart Promo'));
		
        if( $this->getRequest()->getParam($this->_objectId) ) {
          
			$product = Mage::getModel('catalog/product')
				->load($this->getRequest()->getParam($this->_objectId));
			Mage::register('current_product', $product);	
		
			$cartexId = Mage::getresourceModel('cartex/cart_entity')
				->fetchbyEntityId($this->getRequest()->getParam($this->_objectId));
		
			if($cartexId){
				$model = Mage::getModel('cartex/cart_entity')
					->load($cartexId);
				Mage::register('cartex_product', $model);
			}	
			else
			{
				Mage::register('cartex_product', array());		
			}
        }
    }
	
    public function getHeaderText()
    {
        if( Mage::registry('cartex_product') && Mage::registry('cartex_product')->getId() ) {
			return Mage::helper('cartex')->__("Edit Product Promo '%s'", $this->htmlEscape(Mage::registry('cartex_product')->getPromoName()));
        } else {
			return Mage::helper('cartex')->__('New Product Promo for '.$this->htmlEscape(Mage::registry('current_product')->getName()));
        }

    }
}
