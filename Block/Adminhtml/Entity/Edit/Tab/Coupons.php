<?php


class Wdc_Cartex_Block_Adminhtml_Entity_Edit_Tab_Coupons extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('related_coupon_grid');
		$this->setDefaultSort('coupon_id');
		$this->setUseAjax(true);
	}

	protected function _addColumnFilterToCollection($column)
	{
		// Set custom filter for in product flag
		if ($column->getId() == 'in_coupons') {
			$couponIds = $this->_getSelectedCoupons();
			if (empty($couponIds)) {
				$couponIds = 0;
			}
			if ($column->getFilter()->getValue()) {
				$this->getCollection()->addFieldToFilter('coupon_id', array('in'=>$couponIds));
			}
			else {
				if($categoryIds) {
					$this->getCollection()->addFieldToFilter('coupon_id', array('nin'=>$couponIds));
				}
			}
		}
		else {
			parent::_addColumnFilterToCollection($column);
		}
		return $this;
	}

	protected function _prepareCollection()	{
		
			
		
		$cartexId = Mage::app()->getFrontController()->getRequest()->get('id');	
		$collection = $this->getData('coupon_collection');
		if (is_null($collection)) {
			$collection = Mage::getModel('cartex/cart_coupon')->getCollection();
							
			//$collection->addFieldToFilter('cartex_id', $cartexId);
			$collection->addFieldToFilter('cartex_id', array('in' =>array(0, $cartexId)));
			$this->setData('coupon_collection', $collection);
		}
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	


	protected function _prepareColumns()
	{
		$this->addColumn('in_coupons', array(
			'header_css_class' => 'a-center',
			'type'      => 'checkbox',
			'name'      => 'in_coupons',
			'values'    => $this->_getSelectedCoupons(),
			'field_name' => 'couponitems[]',
			'align'     => 'center',
			'index'     => 'coupon_id'
			));

		$this->addColumn('coupon_id', array(
			'header'    => Mage::helper('catalog')->__('IDs'),
			'sortable'  => true,
			'name'		=> 'coupon_id',
			'width'     => '60px',
			'index'     => 'coupon_id'
			));
		
		$this->addColumn('coupon_code', array(
			'header'    => Mage::helper('catalog')->__('Coupon Codex'),
			'index'     => 'coupon_code',
			//'type'      => 'textbox',
			//'field_name' => 'couponcodes[]',
			//'editable'		=> true
			));
			
		$this->addColumn('rule_id', array(
			'header'    => Mage::helper('catalog')->__('Rule Id'),
			'index'     => 'rule_id'
			));
			
		$this->addColumn('is_current', array(
			'header'    => Mage::helper('catalog')->__('Status'),
			'index'     => 'is_current',
			'type'      => 'options',
			'options'   => array(
						0 => Mage::helper('catalog')->__('Disabled'),
						1 => Mage::helper('catalog')->__('Enabled'),						
						),
					));
		
		return parent::_prepareColumns();
	}

	public function getGridUrl()
	{
		return $this->getData('grid_url') ? $this->getData('grid_url') : $this->getUrl('*/*/coupons', array('_current'=>true));
	}
	
//	protected function _prepareMassaction()
//	{
//		$this->setMassactionIdField('coupon_id');
//		$this->getMassactionBlock()->setFormFieldName('coupon_generator');		
//		
//		$this->getMassactionBlock()->addItem('coupon_mass', array(
//			'label'        => Mage::helper('cartex')->__('Download'),
//			'url'          => $this->getUrl('*/*/exportCsv')			
//			));
////		$this->getMassactionBlock()->addItem('import', array(
////			'label'        => Mage::helper('cartex')->__('Import From Magento'),
////			'url'          => $this->getUrl('*/*/importcoupons')
////			));
//			
//		$this->getMassactionBlock()->addItem('updatecode', array(
//			'label'        => Mage::helper('cartex')->__('Update Coupon Codes'),
//			'url'          => $this->getUrl('*/adminhtml_coupons/updatecodes')
//			));
//		$this->getMassactionBlock()->addItem('delete_coupons', array(
//			'label'        => Mage::helper('cartex')->__('Delete Selected'),
//			'url'          => $this->getUrl('*/*/deletecoupons')
//			));
//
//		return $this;
//	}

	protected function _getSelectedCoupons()
	{		
		$coupons = $this->getRequest()->getPost('coupons', null);
		if (!is_array($coupons)) {
			
			$cartexId = Mage::app()->getFrontController()->getRequest()->get('id');			
			$cartexCoupons = Mage::getResourceModel('cartex/cart_coupon');
			$coupons = $cartexCoupons->fetchbyCartexId($cartexId);
		}
		return $coupons;
	}    
}