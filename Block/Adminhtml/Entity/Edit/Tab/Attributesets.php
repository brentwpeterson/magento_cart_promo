<?php


class Wdc_Cartex_Block_Adminhtml_Entity_Edit_Tab_Attributesets extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('related_attributes_grid');
		$this->setDefaultSort('attribute_set_id');
		$this->setUseAjax(true);
	}

	protected function _addColumnFilterToCollection($column)
	{
		// Set custom filter for in product flag
		if ($column->getId() == 'in_attributes') {
			$attributesetIds = $this->_getSelectedAttribs();
			if (empty($attributesetIds)) {
				$attributesetIds = 0;
			}
			if ($column->getFilter()->getValue()) {
				$this->getCollection()->addFieldToFilter('attribute_set_id', array('in'=>$attributesetIds));
			}
			else {
				if($attributesetIds) {
					$this->getCollection()->addFieldToFilter('attribute_set_id', array('nin'=>$attributesetIds));
				}
			}
		}
		else {
			parent::_addColumnFilterToCollection($column);
		}
		return $this;
	}

	protected function _prepareCollection()	{
	
		$collection = Mage::getresourceModel('eav/entity_attribute_set_collection');		
		$collection->addFieldToFilter('attribute_set_name', array('neq' =>'Default'));
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	protected function _prepareColumns()
	{
		$this->addColumn('in_attributes', array(
			'header_css_class' => 'a-center',
			'type'      => 'checkbox',
			'name'      => 'in_attributes',
			'values'    => $this->_getSelectedAttribs(),
			'field_name' => 'attributeitems[]',
			'align'     => 'center',
			'index'     => 'attribute_set_id'
			));

		$this->addColumn('attribute_set_id', array(
			'header'    => Mage::helper('catalog')->__('IDs'),
			'sortable'  => true,
			'name'		=> 'attribute_set_id',
			'width'     => '60px',
			'index'     => 'attribute_set_id'
			));
		
		$this->addColumn('attribute_set_name', array(
			'header'    => Mage::helper('catalog')->__('Attribute Set Name (Does not include DEFAULT!)'),
			'index'     => 'attribute_set_name'
			));		

		
		return parent::_prepareColumns();
	}

	public function getGridUrl()
	{
		return $this->getData('grid_url') ? $this->getData('grid_url') : $this->getUrl('*/*/attributes', array('_current'=>true));
	}
	
	protected function _getSelectedAttribs()
	{		
		$attributes = $this->getRequest()->getPost('attributeitems', null);
		if (!is_array($attributes)) {
			
			$cartexId = Mage::app()->getFrontController()->getRequest()->get('id');			
			$attributes = Mage::getResourceModel('cartex/cart_groups')->fetchAttributeSetbyCartexId($cartexId);			
		}
		return $attributes;
	}    
}