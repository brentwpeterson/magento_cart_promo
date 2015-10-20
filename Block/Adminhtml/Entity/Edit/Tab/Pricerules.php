<?php

class Wdc_Cartex_Block_Adminhtml_Entity_Edit_Tab_Pricerules extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $cert = Mage::registry('cartex_data');
        $hlp = Mage::helper('cartex');
        $id = $this->getRequest()->getParam('id');
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('entity_form', array(
            'legend'=>$hlp->__('Cart Promo Info')
        ));
      			
		$fieldset->addField('min_val', 'text', array(
			'name'      => 'min_val',
			'label'     => $hlp->__('Minimum Value'),
			'class'     => 'validate-number',
			'value'	=> '0',
			'required'  => true,
			));	
			
		$fieldset->addField('max_val', 'text', array(
			'name'      => 'max_val',
			'label'     => $hlp->__('Maximum Value'),
			'class'     => 'validate-number',
			'value'	=> '0',
			'required'  => true,
			));	
		
		$fieldset->addField('qual_statement', 'select', array(
			'name'      => 'qual_statement',
                'label'     => $hlp->__('Qualifying Statement'),
                'options'   => array(
                    'between' => $hlp->__('Between'),
//					'gtr' => $hlp->__('Greater than'),
//					'less' => $hlp->__('Less than'),
                ),
            ));
		
        if (Mage::registry('cartex_valuedata')) {
            $form->setValues(Mage::registry('cartex_valuedata')->getData());
        }

        return parent::_prepareForm();
    }	
	

	
}