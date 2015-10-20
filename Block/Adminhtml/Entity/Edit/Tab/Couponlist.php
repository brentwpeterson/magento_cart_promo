<?php

class Wdc_Cartex_Block_Adminhtml_Entity_Edit_Tab_Couponlist extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $cert = Mage::registry('cartex_data');
        $hlp = Mage::helper('cartex');
        $id = $this->getRequest()->getParam('id');
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('couponlist_form', array(
            'legend'=>$hlp->__('Cart List Info')
        ));
      	
		$couponCnt = count($this->getCoupons());
			
		
		if($couponCnt > 0){
			
			foreach ($this->getCoupons() as $coupon){		
				$fieldset->addField('coupon_'.$coupon->getCouponId(), 'text', array(
					'name'      => 'coupon_'.$coupon->getCouponId(),
					'label'     => $hlp->__('Coupon Rule ID '.$coupon->getRuleId()),					
					));				
			}			
		}
		
        if (Mage::registry('cartex_valuedata')) {
			$form->setValues($this->setFormData());
        }
		else
			{
			$form->setValues();	
			}

        return parent::_prepareForm();
    }	
	
	protected function setFormData()
	{
		$data = array();
		$values = array();
		$keys = array();
		
	//	if(isset($this->getCoupons)){
			foreach ($this->getCoupons() as $coupon){		
				
				$keys[]= 'coupon_'.$coupon->getCouponId();
				$values[] = $coupon->getCouponCode();				
			}	
			
		if(count($keys) == count($values) && count($keys) > 0){
				$data = array_combine($keys, $values);
			}
	//	}
		return $data;
	}
	
	protected function getCoupons()
	{
		$cartexId = Mage::app()->getFrontController()->getRequest()->get('id');	
		$collection = Mage::getModel('cartex/cart_coupon')->getCollection();		
		$collection->addFieldToFilter('cartex_id', array('in' =>array(0, $cartexId)));	
		
		return $collection;		
	}
	
	

	
}