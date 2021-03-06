<?php

class Wdc_Cartex_Block_Adminhtml_Promo_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('productpromoGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
      
    }

    protected function _prepareCollection()
	{
		$collection = Mage::getModel('catalog/product_link')->useRelatedLinks()
			->getProductCollection()
			->addFieldToFilter('type_id', array('neq'=>'grouped'))
			//            ->setProduct($this->_getProduct())
			->addAttributeToSelect('*');
		
		$iProductPriceTypeId = Mage::getSingleton('eav/entity_attribute')->getIdByCode('catalog_product', 'price_type');            

		$resource = Mage::getResourceSingleton('bundle/bundle');
		$sAttrTableName = $resource->getTable('catalog/product') . '_int';
		
		$collection->getSelect()->joinLeft($sAttrTableName." AS att"," att.entity_id = e.entity_id AND att.attribute_id = " . $iProductPriceTypeId, array('value'));
		
		$collection->getSelect()->where('e.type_id != "bundle" OR att.value = 1');
		
		#d($collection->getSelect()->__toString(), 1);

		$this->setCollection($collection);
		return parent::_prepareCollection();
		
	}
	/**
	 * Checks when this block is readonly
	 *
	 * @return boolean
	 */
	public function isReadonly()
	{
		//return $this->_getProduct()->getRelatedReadonly();
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

		$this->addColumn('entity_id', array(
			'header'    => Mage::helper('catalog')->__('ID'),
			'sortable'  => true,
			'width'     => '60px',
			'index'     => 'entity_id',
			//'values'    => array(1,2,3,4)
			));
			
		$this->addColumn('name', array(
			'header'    => Mage::helper('catalog')->__('Name'),
			'index'     => 'name'
			));

		$this->addColumn('type',
			array(
					'header'=> Mage::helper('catalog')->__('Type'),
					'width' => '100px',
					'index' => 'type_id',
					'type'  => 'options',
					'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
					));

		$sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
			->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
			->load()
			->toOptionHash();

		$this->addColumn('set_name',
			array(
					'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
					'width' => '130px',
					'index' => 'attribute_set_id',
					'type'  => 'options',
					'options' => $sets,
					));

		$this->addColumn('status',
			array(
					'header'=> Mage::helper('catalog')->__('Status'),
					'width' => '90px',
					'index' => 'status',
					'type'  => 'options',
					'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
					));

		$this->addColumn('visibility',
			array(
					'header'=> Mage::helper('catalog')->__('Visibility'),
					'width' => '90px',
					'index' => 'visibility',
					'type'  => 'options',
					'options' => Mage::getSingleton('catalog/product_visibility')->getOptionArray(),
					));

		$this->addColumn('sku', array(
			'header'    => Mage::helper('catalog')->__('SKU'),
			'width'     => '80px',
			'index'     => 'sku'
			));
		$this->addColumn('price', array(
			'header'    => Mage::helper('catalog')->__('Price'),
			'type'  => 'currency',
			'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
			'index'     => 'price'
			));

		/*$this->addColumn('qty', array(
		    'header'    => Mage::helper('catalog')->__('Default Qty'),
		    'name'      => 'qty',
		    'align'     => 'center',
		    'type'      => 'number',
		    'validate_class' => 'validate-number',
		    'index'     => 'qty',
		    'width'     => '60px',
		    'editable'  => true
		));*/

//		$this->addColumn('position', array(
//			'header'    => Mage::helper('catalog')->__('Position'),
//			'name'      => 'position',
//			'type'      => 'number',
//			'validate_class' => 'validate-number',
//			'index'     => 'position',
//			'width'     => '60px',
//			//'editable'  => !$this->isReadonly(),
//			'edit_only' => !$this->_getProduct()->getId()
//			));

		return parent::_prepareColumns();
	}

//	public function getGridUrl()
//	{
//		return $this->getData('grid_url') ? $this->getData('grid_url') : $this->getUrl('cartex/adminhtml_entity/productstab', array('_current'=>true));
//	}

	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
	
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}
