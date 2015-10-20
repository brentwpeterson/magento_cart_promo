<?php


class Wdc_Cartex_Block_Adminhtml_Entity_Edit_Tab_Categories extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('related_category_grid');
		$this->setDefaultSort('entity_id');
		$this->setUseAjax(true);
	}

	protected function _addColumnFilterToCollection($column)
	{
		// Set custom filter for in product flag
		if ($column->getId() == 'in_categorys') {
			$categoryIds = $this->_getSelectedCats();
			if (empty($categoryIds)) {
				$categoryIds = 0;
			}
			if ($column->getFilter()->getValue()) {
				$this->getCollection()->addFieldToFilter('entity_id', array('in'=>$categoryIds));
			}
			else {
				if($categoryIds) {
					$this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$categoryIds));
				}
			}
		}
		else {
			parent::_addColumnFilterToCollection($column);
		}
		return $this;
	}

	protected function _prepareCollection()	{
	
		$storeId = $this->getRequest()->getParam('store', $this->_getDefaultStoreId());
		$collection = $this->getData('category_collection');
		if (is_null($collection)) {
			$collection = Mage::getModel('catalog/category')->getCollection();
			$collection->addAttributeToSelect('name')
				->addAttributeToSelect('level')
				->addAttributeToSelect('is_active')
				//->setProductStoreId($storeId)			
				->setStoreId($storeId);		
				
			$collection->addFieldToFilter('name', array('neq' =>''));
			$collection->addFieldToFilter('level', array('nin' =>array(0, 1)));
			$this->setData('category_collection', $collection);
		}
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	
	protected function _getDefaultStoreId()
	{
		return Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
	}

	protected function _prepareColumns()
	{
		$this->addColumn('in_categorys', array(
			'header_css_class' => 'a-center',
			'type'      => 'checkbox',
			'name'      => 'in_categorys',
			'values'    => $this->_getSelectedCats(),
			'field_name' => 'items[]',
			'align'     => 'center',
			'index'     => 'entity_id'
			));

		$this->addColumn('entity_id', array(
			'header'    => Mage::helper('catalog')->__('IDs'),
			'sortable'  => true,
			'name'		=> 'entity_id',
			'width'     => '60px',
			'index'     => 'entity_id'
			));
		
		$this->addColumn('name', array(
			'header'    => Mage::helper('catalog')->__('Category Name'),
			'index'     => 'name'
			));
			
		$this->addColumn('level', array(
			'header'    => Mage::helper('catalog')->__('Category Level'),
			'index'     => 'level'
			));
			
		$this->addColumn('is_active', array(
			'header'    => Mage::helper('catalog')->__('Status'),
			'index'     => 'is_active',
			'type'      => 'options',
			'options'   => array(
						0 => Mage::helper('catalog')->__('Disabled'),
						1 => Mage::helper('catalog')->__('Enabled'),						
						),
					));

		//            $this->addColumn('status',
		//            array(
		//                'header'=> Mage::helper('catalog')->__('Status'),
		//                'width' => '90px',
		//                'index' => 'status',
		//                'type'  => 'options',
		//                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
		//        ));

		
		return parent::_prepareColumns();
	}

	public function getGridUrl()
	{
		return $this->getData('grid_url') ? $this->getData('grid_url') : $this->getUrl('*/*/categories', array('_current'=>true));
	}

	protected function _getSelectedCats()
	{		
		$categorys = $this->getRequest()->getPost('categorys', null);
		if (!is_array($categorys)) {
			
			$cartexId = Mage::app()->getFrontController()->getRequest()->get('id');			
			$cartexProducts = Mage::getResourceModel('cartex/cart_groups');
			$categorys = $cartexProducts->fetchbyAttributeId($cartexId, Mage::getModel('eav/entity_type')->loadByCode('catalog_category')->getId());
		}
		return $categorys;
	}    
}