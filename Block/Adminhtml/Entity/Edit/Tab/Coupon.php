<?php

class Wdc_Cartex_Block_Adminhtml_Entity_Edit_Tab_Coupon extends Mage_Adminhtml_Block_Widget_Form
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
				
/*		$fieldset->addField('use_rules', 'select', array(
			'name'      => 'use_rules',
			'label'     => $hlp->__('Use Rule Dates'),			     
			'options'   => array(						
						0 => $hlp->__('No'),
						1 => $hlp->__('Yes'),
						),
					));	*/	
		
//		/***Use this with Rule Id? **/
//		$fieldset->addField('rule_id', 'select', array(
//			'name'      => 'rule_id',
//			'label'     => $hlp->__('Associated Rule?'),
//			'options'   => $this->getAllRules(),
//		));
		
		$fieldset->addField('discount_amount', 'text', array(
			'name'  => 'discount_amount',
			'label' => Mage::helper('cartex')->__('Discount Amount'),
			'title' => Mage::helper('cartex')->__('Discount Amount'),
			'note'  => Mage::helper('cartex')->__('Please don\'t make your discount greater than the cost of your item!'),
			'class'     => 'validate-number',
			'required' => false,			
			));

		
        if (Mage::registry('cartex_data')) {
            $form->setValues(Mage::registry('cartex_data')->getData());
        }

        return parent::_prepareForm();
    }	
	
	protected function getAllRules()
	{		
		$model = Mage::getModel('cartex/rules')->getRuleModel();
		$collection = $model->getCollection();
		
		$keys = '';
		$values = '';
		
		if(Mage::getModel('cartex/rules')->getVersion() == 2){
			foreach ($collection as $item)
			{
				if(!empty($item['code'])){
					$keys.= $item['rule_id'].',';
					$values.= $item['code'].'->'.$item['name'].',';
				}
			}
		}
		else{		
			foreach ($collection as $item)
			{
				if(!empty($item['coupon_code'])){
					$keys.= $item['rule_id'].',';
					$values.= $item['coupon_code'].'->'.$item['name'].',';
				}
			}
		}
		return array_combine(explode(',',$keys), explode(',',$values));		
	}
	
}