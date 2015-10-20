<?php

class Wdc_Cartex_Block_Adminhtml_Coupon_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('couponcodeGrid');
        $this->setDefaultSort('coupon_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
      
    }

    protected function _prepareCollection()
	{
		$collection = Mage::getModel('cartex/cart_coupon')->getCollection();
			//->addFieldToFilter('cartex_id', array('in'=>'grouped'))
		
		$this->setCollection($collection);
		return parent::_prepareCollection();
		
	}

	protected function _prepareColumns()
	{
//		if (!$this->isReadonly()) {
//			$this->addColumn('in_products', array(
//				'header_css_class' => 'a-center',
//				'type'      => 'checkbox',
//				'name'      => 'in_products',
//				'values'    => $this->_getSelectedProducts(),
//				'align'     => 'center',
//				'index'     => 'entity_id'
//				));
//		}

		$this->addColumn('coupon_id', array(
			'header'    => Mage::helper('catalog')->__('ID'),
			'sortable'  => true,
			'width'     => '60px',
			'index'     => 'coupon_id',
			//'values'    => array(1,2,3,4)
			));
			
		$this->addColumn('coupon_code', array(
			'header'    => Mage::helper('catalog')->__('Coupon Code'),
			'editable'		=> true,		
			'edit_only'		=> true,
			//'width'     => '120px',			
			'index'     => 'coupon_code',
			'field_name' => 'couponitems[]',
		//	'width'             => 200,
			));			

		return parent::_prepareColumns();
	}

//	public function getRowUrl($row)
//	{
//		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
//	}
//	
//    public function getGridUrl()
//    {
//        return $this->getUrl('*/*/grid', array('_current'=>true));
//    }

		protected function _prepareMassaction()
		{
			$this->setMassactionIdField('coupon_code');
			
			$this->getMassactionBlock()->setFormFieldName('coupon_generator');		
			
//			$this->getMassactionBlock()->addItem('coupon_mass', array(
//				'label'        => Mage::helper('cartex')->__('Download'),
//				'url'          => $this->getUrl('*/*/exportCsv')			
//				));
	//		$this->getMassactionBlock()->addItem('import', array(
	//			'label'        => Mage::helper('cartex')->__('Import From Magento'),
	//			'url'          => $this->getUrl('*/*/importcoupons')
	//			));
				
			$this->getMassactionBlock()->addItem('updatecode', array(
				'label'        => Mage::helper('cartex')->__('Update Coupon Codes'),
				'url'          => $this->getUrl('*/adminhtml_coupon/updatecodes')
				));
//			$this->getMassactionBlock()->addItem('delete_coupons', array(
//				'label'        => Mage::helper('cartex')->__('Delete Selected'),
//				'url'          => $this->getUrl('*/*/deletecoupons')
//				));
	
			return $this;
		}
}
