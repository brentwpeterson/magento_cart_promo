<?php

class Wdc_Cartex_Block_Adminhtml_Entity_Edit_Tab_Coupgen extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $cert = Mage::registry('cartex_data');
        $hlp = Mage::helper('cartex');
        $id = $this->getRequest()->getParam('id');
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('coupgen_form', array(
            'legend'=>$hlp->__('Cartex Coupon Generator')
        ));
			
		$fieldset->addField('coupon_text', 'note', array(
			'name'  => 'coupon_text',
			//'label' => Mage::helper('cartex')->__('Coupon Length'),
			//'title' => Mage::helper('cartex')->__('Coupon Length'),
			//'note'  => Mage::helper('cartex')->__('Your Coupon length can between 4 and 10 charactors'),
			//'class'     => 'validate-number',
			//'required' => false,			
			));	
		
		$fieldset->addField('coupon_len', 'text', array(
			'name'  => 'coupon_len',
			'label' => Mage::helper('cartex')->__('Coupon Length'),
			'title' => Mage::helper('cartex')->__('Coupon Length'),
			'note'  => Mage::helper('cartex')->__('Your Coupon length can between 4 and 10 charactors'),
			'class'     => 'validate-number',
			'required' => false,			
			));
			
		$fieldset->addField('code_prefix', 'text', array(
			'name'  => 'code_prefix',
			'label' => Mage::helper('catalog')->__('Code Prefix'),
			'title' => Mage::helper('catalog')->__('Code Prefix'),
			'note'  => Mage::helper('catalog')->__('Your prefix will start your coupon, i.e. MAR'),
			'class' => 'validate-code',
			'required' => false,
			));
			
		$fieldset->addField('coupon_num', 'text', array(
			'name'  => 'coupon_num',
			'label' => Mage::helper('cartex')->__('Number of Coupons'),
			'title' => Mage::helper('cartex')->__('Number of Coupons'),
			'note'  => Mage::helper('cartex')->__('Must be greater than 1'),
			'class'     => 'validate-number',
			'required' => false,			
			));
			
		$fieldset->addField('coupon_use', 'text', array(
			'name'  => 'coupon_use',
			'label' => Mage::helper('cartex')->__('Coupon Uses'),
			'title' => Mage::helper('cartex')->__('Coupon Uses'),
			'note'  => Mage::helper('cartex')->__('If unlimited leave at 0'),
			'class'     => 'validate-number',
			'value'	=> '0',
			'required' => false,			
			));
			
		$fieldset->addField('cust_use', 'text', array(
			'name'  => 'cust_use',
			'label' => Mage::helper('cartex')->__('Customer Uses'),
			'title' => Mage::helper('cartex')->__('Customer Uses'),
			'note'  => Mage::helper('cartex')->__('If unlimited leave at 0'),
			'class'     => 'validate-number',
			'value'	=> '0',
			'required' => false,			
			));
			
		$fieldset->addField('coupon_discount', 'text', array(
			'name'  => 'coupon_discount',
			'label' => Mage::helper('cartex')->__('Discount'),
			'title' => Mage::helper('cartex')->__('Discount'),
			'note'  => Mage::helper('cartex')->__('Leave at 0 if not used'),
			'class'     => 'validate-number',
			'value'	=> '0',
			'required' => false,			
			));
			
		$fieldset->addField('add_button', 'note', array(
			'text' => $this->getLayout()->createBlock('adminhtml/widget_button')
			->setData(array(
							'label'     => Mage::helper('cartex')->__('Create Codes'),
							'onclick'   => 'sendcodes();',
							'class'     => 'add',
							))->toHtml(),
					'no_span'   => true,
					)
				);
		$fieldset->addField('cartex_id', 'hidden', array(
			'name'  => 'cartex_id',
			'value' => $this->getRequest()->getParam('id'),
			));
			
			
		$field2set = $form->addFieldset('couplist_form', array(
			'legend'=>$hlp->__('Attached Coupon List')
			));
		
		$field2set->addField('coupon_list', 'note', array(
			//'label'     => Mage::helper('cartex')->__('Ct'),
			'name'  => 'coupon_list',			
			));	
			
		$field2set->addField('add_button1', 'note', array(
			'text' => $this->getLayout()->createBlock('adminhtml/widget_button')
			->setData(array(
							'label'     => Mage::helper('cartex')->__('Update Code List'),
							'onclick'   => 'updatecodes();',
							'class'     => 'add',
							))->toHtml(),
					'no_span'   => true,
					)
				);
				
/*		$fieldset->addField('use_rules', 'select', array(
			'name'      => 'use_rules',
			'label'     => $hlp->__('Use Rule Dates'),			     
			'options'   => array(						
						0 => $hlp->__('No'),
						1 => $hlp->__('Yes'),
						),
					));	*/	
		
		/***Use this with Rule Id? **/
//		$fieldset->addField('rule_id', 'select', array(
//			'name'      => 'rule_id',
//			'label'     => $hlp->__('Associated Rule?'),
//			'options'   => $this->getAllRules(),
//		));
		


		
        if (Mage::registry('cartex_data')) {
            $form->setValues(Mage::registry('cartex_data')->getData());
        }

        return parent::_prepareForm();
    }	
	
	
	
}