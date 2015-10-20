<?php

class Wdc_Cartex_Block_Adminhtml_Entity_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected $_promotype;
	protected $_cartexId;
	
    protected function _prepareForm()
    {
       // $cert = Mage::registry('cartex_data');      
		$this->_cartexId = Mage::app()->getFrontController()->getRequest()->get('id');	
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('entity_form', array(
            'legend'=>Mage::helper('cartex')->__('Cart Promo Info')
        ));

        $fieldset->addField('promo_name', 'text', array(
            'name'      => 'promo_name',
            'label'     => Mage::helper('cartex')->__('Promo Name'),
            'class'     => 'required-entry',
            'required'  => true,
             ));
		
		$fieldset->addField('description', 'textarea', array(
			'name'      => 'description',
			'label'     => Mage::helper('cartex')->__('Promo Description'),          
			));
		
		if($this->getPromoType() == 11){	
		$fieldset->addField('error_text', 'textarea', array(
			'name'  => 'promo_code',
			'label' => Mage::helper('cartex')->__('Error text'),
			'title' => Mage::helper('cartex')->__('Error text'),		
			));
			
			
		}

			
		$fieldset->addField('promo_type', 'select', array(
				'name'      => 'promo_type',
                'label'     => Mage::helper('cartex')->__('Promo Type'),
                'options'   => array(
                    0 => Mage::helper('cartex')->__('Value based Rule'),
					//1 => Mage::helper('cartex')->__('X to Y'),
					//2 => Mage::helper('cartex')->__('Coupon Code Rule-Value Based'),
					//3 => Mage::helper('cartex')->__('Coupon Code Rule-X for Y'),
					//4 => Mage::helper('cartex')->__('Grouped Products'),
					//6 => Mage::helper('cartex')->__('Value > X to Y'),
					//7 => Mage::helper('cartex')->__('Coupon > Value > X to Y'),
					//8 => Mage::helper('cartex')->__('Coupon only'),
					//9 => Mage::helper('cartex')->__('Coupon > Discount'),
					//10 => Mage::helper('cartex')->__('X for Discounted Y'),
					//11 => Mage::helper('cartex')->__('Free Gift Chooser'),
					//12 => Mage::helper('cartex')->__('Buy something get something free'),
					
                ),
            ));
			
		if($this->getPromoType() == 1 || $this->getPromoType() == 3  || $this->getPromoType() == 6  || $this->getPromoType() == 9 || $this->getPromoType() == 11) {
			$fieldset->addField('exception_type_id', 'select', array(
				'name'      => 'exception_type_id',
				'label'     => Mage::helper('cartex')->__('Exception Type'),
				//'class'     => 'required-entry',          
				'options'   => array(						
							0 => Mage::helper('cartex')->__('Product'),
							1 => Mage::helper('cartex')->__('Category'),
							2 => Mage::helper('cartex')->__('Attribute Set'),
							),
						));
		}
		
		
		$fieldset->addField('discount_amount', 'text', array(
			'name'  => 'discount_amount',
			'label' => Mage::helper('cartex')->__('Discount Amount'),
			'title' => Mage::helper('cartex')->__('Discount Amount'),
			'note'  => Mage::helper('cartex')->__('Please don\'t make your discount greater than the cost of your item! If you 
					are using a free item leave the discount at 0'),
			'class'     => 'validate-number',
			'value'	=> '0',
			'required' => false,			
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
					
		/*$fieldset->addField('use_rules', 'select', array(
				'name'      => 'use_rules',
				'label'     => Mage::helper('cartex')->__('One to One?'),
				'class'     => 'required-entry',          
				'options'   => array(						
							0 => Mage::helper('cartex')->__('No'),
							1 => Mage::helper('cartex')->__('Yes'),
							),
						));*/
						
//			$fieldset->addField('stop_rules', 'select', array(
//				'name'      => 'stop_rules',
//				'label'     => Mage::helper('cartex')->__('Stop Rule Processing? (beta)'),
//				'class'     => 'required-entry', 
//				'note'  => Mage::helper('cartex')->__('This will halt any further rules from being processed'),         
//				'options'   => array(						
//							0 => Mage::helper('cartex')->__('No'),
//							1 => Mage::helper('cartex')->__('Yes'),
//							),
//						));
			
//		$fieldset->addField('item_limit', 'text', array(
//			'name'  => 'item_limit',
//			'label' => Mage::helper('cartex')->__('Item limit'),
//			'title' => Mage::helper('cartex')->__('Item limi'),
//			'note'  => Mage::helper('cartex')->__('Must be a number greater than 0'),
//			'class'     => 'validate-number',
//			'value'	=> '1',
//			'required' => true,
//			//'after_element_html' => '<p class="nm"><small>' . Mage::helper('cartex')->__('(Use this for BuyX get Free rule or for Free Gift Limit)') . '</small></p>',
//			));
			

//		$dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
//		$fieldset->addField('from_date', 'date', array(
//			'name'   => 'from_date',
//			'label'  => Mage::helper('cartex')->__('From Date'),
//			'title'  => Mage::helper('cartex')->__('From Date'),
//			'image'  => $this->getSkinUrl('images/grid-cal.gif'),
//			'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
//			'format'       => $dateFormatIso
//			));
//		$fieldset->addField('to_date', 'date', array(
//			'name'   => 'to_date',
//			'label'  => Mage::helper('cartex')->__('To Date'),
//			'title'  => Mage::helper('cartex')->__('To Date'),
//			'image'  => $this->getSkinUrl('images/grid-cal.gif'),
//			'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
//			'format'       => $dateFormatIso
//			));

        $fieldset->addField('store_id', 'select', array(
            'name'      => 'store_id',
            'label'     => Mage::helper('core')->__('Store View'),
            'title'     => Mage::helper('core')->__('Store View'),
            'required'  => true,
            'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
        ));


        if (Mage::registry('cartex_data')) {
            $form->setValues(Mage::registry('cartex_data')->getData());
        }
        return parent::_prepareForm();
    }	
	
	protected function getPromoType()
	{
		$this->_promotype =  Mage::getModel('cartex/cart_entity')->load($this->_cartexId)->getPromoType();
		return $this->_promotype;
	}

	
}