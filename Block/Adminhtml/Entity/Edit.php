<?php

class Wdc_Cartex_Block_Adminhtml_Entity_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'cartex';
        $this->_controller = 'adminhtml_entity';

        $this->_updateButton('save', 'label', Mage::helper('cartex')->__('Save Cart Promo'));
        $this->_updateButton('delete', 'label', Mage::helper('cartex')->__('Delete Cart Promo'));
		


        if( $this->getRequest()->getParam($this->_objectId) ) {
            $model = Mage::getModel('cartex/cart_entity')
                ->load($this->getRequest()->getParam($this->_objectId));
            Mage::register('cartex_data', $model);
			
			$valueId = Mage::getresourceModel('cartex/cart_value')->fetchbyAttributeId($model->getId());
			$valueModel = Mage::getModel('cartex/cart_value')->load($valueId);
			Mage::register('cartex_valuedata', $valueModel);
        }



    }
	
//	protected function _prepareLayout()
//	{
//		parent::_prepareLayout();
//		$this->setChild('new_button',
//			$this->getLayout()->createBlock('adminhtml/widget_button')
//			->setData(array(
//						'label'     => Mage::helper('adminhtml')->__('Re-Index Categories'),
//						'onclick'   => "setLocation('".$this->getUrl('*/*/categoryindex', array('template_id' => 0))."')",
//						'class'     => 'scalable add'
//						))
//				);
//		return $this;
//	}

    public function getHeaderText()
    {
        if( Mage::registry('cartex_data') && Mage::registry('cartex_data')->getId() ) {
			return Mage::helper('cartex')->__("Edit Cart Promo '%s'", $this->htmlEscape(Mage::registry('cartex_data')->getPromoName()));
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
