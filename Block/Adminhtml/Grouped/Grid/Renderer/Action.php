<?php

class Wdc_Cartex_Block_Adminhtml_Grouped_Grid_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $urlModel = Mage::getModel('core/url')->setStore($row->getData('_first_store_id'));
        $href = $urlModel->getUrl('', array('_current'=>false)) . "{$row->getIdentifier()}?___store={$row->getStoreCode()}";
        return '<a href="'.$href.'" target="_blank">'.$this->__('Preview').'</a>';
    }
}