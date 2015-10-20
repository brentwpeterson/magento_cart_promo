<?php

class Wdc_Cartex_Block_Adminhtml_Entity_Edit_Tab_Exception extends Mage_Adminhtml_Block_Widget_Form
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
			
		/***Use this with Rule Id? **/
		$fieldset->addField('attribute_set_id', 'multiselect', array(
			'name'      => 'attribute_set_id',
			'label'     => $hlp->__('Attribute Set '),
			'values'   => $this->getAttributeSets(),
					));


        if (Mage::registry('cartex_data')) {
            $form->setValues(Mage::registry('cartex_data')->getData());
        }

        return parent::_prepareForm();
    }	
	
	protected function getAttributeSets()
	{
//		$collection = Mage::getModel('eav/entity_attribute_set')->getCollection();
//		
//		$keys = '';
//		$values = '';
//		
//		foreach ($collection as $item)
//		{
//			if($item->getAttributeSetName() != 'Default'){
//				$keys.= $item->getAttributeSetId().',';
//				$values.= $item->getAttributeSetName().',';
//			}
//		}
//		
//		return array_combine(explode(',',$keys), explode(',',$values));	
//		
		
		$collection = Mage::getModel('eav/entity_attribute_set')->getCollection();
		
		$options = array();
		
	
		foreach ($collection as $group) {
			
			if($group->getAttributeSetName() != 'Default'){
				$options[] = array(
					'label' => $group->getAttributeSetName(),
					'value' => $group->getAttributeSetId()
					);
			}
		}
		
		return $options;
	}
	
}