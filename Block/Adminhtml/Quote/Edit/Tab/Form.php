<?php

class Wdc_Cartex_Block_Adminhtml_Quote_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{

	protected function _prepareForm()
	{
		$model = Mage::registry('coupon_data');

		$couponId = Mage::app()->getFrontController()->getRequest()->get('id');	
		$form = new Varien_Data_Form();
		$this->setForm($form);
		
		$fieldset = $form->addFieldset('quote_form', array(
			'legend'=>Mage::helper('cartex')->__('Coupon to duplicate: '.Mage::registry('coupon_data')->getName())
			));
			
		if ($model->getId()) {
			$fieldset->addField('rule_id', 'hidden', array(
				'name' => 'rule_id',
				));
		}

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

		if (Mage::registry('coupon_data')) {
			$form->setValues(Mage::registry('coupon_data')->getData());
		}
		return parent::_prepareForm();
	}	
	
}