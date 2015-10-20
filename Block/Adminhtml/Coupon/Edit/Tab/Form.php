<?php

class Wdc_Cartex_Block_Adminhtml_Promo_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
       // $cert = Mage::registry('cartex_data');
       // $hlp = Mage::helper('cartex');
       // $id = $this->getRequest()->getParam('id');
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('entity_form', array(
			'legend'=>Mage::helper('cartex')->__('Cart Promo Info '.$this->htmlEscape(Mage::registry('current_product')->getName()))
        ));

        $fieldset->addField('promo_name', 'text', array(
            'name'      => 'promo_name',
            'label'     => Mage::helper('cartex')->__('Promo Name'),
            'class'     => 'required-entry',
            'required'  => true,
             ));
			
		$fieldset->addField('promo_code', 'text', array(
			'name'  => 'promo_code',
			'label' => Mage::helper('catalog')->__('Promo Code'),
			'title' => Mage::helper('catalog')->__('Promo Code'),
			'note'  => Mage::helper('catalog')->__('For internal use. Must be unique with no spaces'),
			'class' => 'validate-code',
			'required' => true,
			));
			
		  $fieldset->addField('description', 'textarea', array(
            'name'      => 'description',
           'label'     => Mage::helper('cartex')->__('Promo Description'),          
             ));
			
		$fieldset->addField('promo_type', 'select', array(
			'name'      => 'promo_type',
			'label'     => Mage::helper('cartex')->__('Promo Type'),
			'options'   => array(
						5 => Mage::helper('cartex')->__('Product Upgrades'),
						4 => Mage::helper('cartex')->__('Grouped Products'),						
						),
						));
			
			
		$fieldset->addField('is_active', 'select', array(
			'name'      => 'is_active',
			'label'     => Mage::helper('cartex')->__('Enabled'),
			'class'     => 'required-entry',          
			'options'   => array(						
						0 => Mage::helper('cartex')->__('Disabled'),
						1 => Mage::helper('cartex')->__('Enabled'),
						),
					));
	
		$fieldset->addField('store_id', 'select', array(
			'name'      => 'store_id',
			'label'     => Mage::helper('core')->__('Store View'),
			'title'     => Mage::helper('core')->__('Store View'),
			'required'  => true,
			'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
			));


		if (Mage::registry('cartex_product')) {
			$form->setValues(Mage::registry('cartex_product')->getData());
		}
		return parent::_prepareForm();
	}	
	
}