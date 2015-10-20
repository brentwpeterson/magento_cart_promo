<?php

class Wdc_Cartex_Block_Adminhtml_Entity_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('entityGrid');
        $this->setDefaultSort('cartex_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);      
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('cartex/cart_entity')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
	
    protected function _prepareColumns()
    {
        $hlp = Mage::helper('cartex');

        $this->addColumn('cartex_id', array(
            'header'    => $hlp->__('Promo ID'),
            'align'     => 'right',
            'width'     => '5px',
            'index'     => 'cartex_id',
          //  'type'      => 'number',
        ));

        $this->addColumn('promo_name', array(
            'header'    => $hlp->__('Promo Name'),
            'align'     => 'left',
            'index'     => 'promo_name',
        ));
		

        $this->addColumn('promo_type', array(
            'header'    => $hlp->__('Promo Type'),
            'index'     => 'promo_type',
            'type'      => 'options',
          //  'filter_index' => 'main_table.status',
            'options'   => array(
						0 => Mage::helper('cartex')->__('Value based Rule'),
						1 => Mage::helper('cartex')->__('X to Y'),
						2 => Mage::helper('cartex')->__('Coupon Code Rule-Value Based'),
						3 => Mage::helper('cartex')->__('Coupon Code Rule-X for Y'),
						//4 => Mage::helper('cartex')->__('Grouped Products'),
						6 => Mage::helper('cartex')->__('Value > X to Y'),
						7 => Mage::helper('cartex')->__('Coupon > Value > X to Y'),
						8 => Mage::helper('cartex')->__('Coupon only'),
						11 => Mage::helper('cartex')->__('Free Gift Chooser'),
						12 => Mage::helper('cartex')->__('Buy something get something free'),
            ),
        ));
		
		$this->addColumn('is_active', array(
			'header'    => $hlp->__('Status'),
			'index'     => 'is_active',
			'type'      => 'options',
			'options'   => array(
						0 => $hlp->__('Disabled'),
						1 => $hlp->__('Enabled'),						
						),
					));

        $this->addColumn('store_id', array(
            'header'    => $this->__('Store View'),
            'width'     => '200px',
            'index'     => 'store_id',
            'type'      => 'store',
            'store_all'  => false,
            'store_view' => true,
        ));
		
		$this->addColumn('item_limit', array(
			'header'    => $hlp->__('Item Limit'),
			'align'     => 'left',
			'index'     => 'item_limit',
			));
		
		$this->addColumn('sort_order', array(
			'header'    => $hlp->__('Sort Order'),
			'align'     => 'left',
			'index'     => 'sort_order',
			));


        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}
