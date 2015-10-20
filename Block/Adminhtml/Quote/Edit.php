<?php


class Wdc_Cartex_Block_Adminhtml_Quote_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

	public function __construct()
	{
		parent::__construct();
		$this->_objectId = 'id';
		$this->_blockGroup = 'cartex';
		$this->_controller = 'adminhtml_quote';

		$this->_updateButton('save', 'label', Mage::helper('cartex')->__('Duplicate Coupon'));
		$this->_updateButton('delete', 'label', Mage::helper('cartex')->__('Delete Coupon'));		


		if( $this->getRequest()->getParam($this->_objectId) ) {
			$model =  Mage::getModel('salesrule/rule')
				->load($this->getRequest()->getParam($this->_objectId));
			Mage::register('coupon_data', $model);
			
//			$valueId = Mage::getresourceModel('cartex/cart_value')->fetchbyAttributeId($model->getId());
//			$valueModel = Mage::getModel('cartex/cart_value')->load($valueId);
//			Mage::register('cartex_valuedata', $valueModel);
		}



	}
	

	public function getHeaderText()
	{
		if( Mage::registry('coupon_data') && Mage::registry('coupon_data')->getId() ) {
			return Mage::helper('cartex')->__("Edit Cart Promo '%s'", $this->htmlEscape(Mage::registry('coupon_data')->getPromoName()));
		} else {
			return Mage::helper('cartex')->__('New Cart Promo');
		}

	}
	
	
	public function getSaveAndContinueUrl()
	{
		return $this->getUrl('*/*/save', array(
			'_current'   => true,
			'back'       => 'edit',
			'tab'        => '{{tab_id}}',
			'active_tab' => null
			));
	}
}


