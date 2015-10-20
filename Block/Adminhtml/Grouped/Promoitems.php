<?php
/**
 * @copyright  Copyright (c) 2010 Wagento Data Consulting 
 */

class Wdc_Cartex_Block_Adminhtml_Grouped_Promoitems extends Mage_Adminhtml_Block_Widget implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    /**
////     * Reference to product objects that is being edited
////     *
////     * @var Mage_Catalog_Model_Product
////     */
    protected $_product = null;

    protected $_config = null;

    public function __construct()
    {
        parent::__construct();		
		$this->setTemplate('wdc/cartex/grid.phtml');		
    }


	protected function _getProduct()
	{
		return Mage::registry('current_product');
	}

    public function getTabLabel()
    {
		return Mage::helper('cartex')->__('Grouped Promo Items');
    }
	
    public function getTabTitle()
    {
		return Mage::helper('cartex')->__('Grouped Promo Items');
    }


    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
/*** Add tab to products ***/

	protected function _toHtml()
//	protected function _prepareLayout()
	{		
		
//		$this->addTab('xcustomer_options', array(
//			'label' => Mage::helper('catalog')->__('XXCustom Options'),
//			'url'   => $this->getUrl('*/*/options', array('_current' => true)),
//			'class' => 'ajax',
//			));
		
		$accordion = $this->getLayout()->createBlock('adminhtml/widget_accordion')
			->setId('cartexGrouped');

		$accordion->addItem('promotemplate', array(
			'title'   => Mage::helper('adminhtml')->__('Choose Promo Template'),
			'content' => $this->getLayout()->createBlock('cartex/adminhtml_grouped_grouped')->toHtml(),
			'open'    => true,
			));
			
		$accordion->addItem('promoproducts', array(
			'title'   => Mage::helper('adminhtml')->__('Grouped Promotional Products'),
			//'content' => $this->getLayout()->createBlock('cartex/adminhtml_entity_edit_tab_productstab')->toHtml(),
			'content' => $this->getLayout()->createBlock('cartex/adminhtml_grouped_grid')->toHtml(),
			//'url'       => $this->getUrl('*/*/added', array('_current' => true)),
			'open'    => true,
			));

		
//		$accordion->addItem('productstab', array(
//			'title'   => Mage::helper('adminhtml')->__('Grouped Promotional Products'),
//				//'label'     => Mage::helper('cartex')->__('Assign Grouped Products'),
//				'url'       => $this->getUrl('cartex/adminhtml_entity/productstab', array('_current' => true)),
//				'class'     => 'ajax',
//				'open'    => true,
//				));
		
		
		$this->setChild('accordion', $accordion);

		return parent::_toHtml();
		//return parent::_prepareLayout();
	}
	
}
