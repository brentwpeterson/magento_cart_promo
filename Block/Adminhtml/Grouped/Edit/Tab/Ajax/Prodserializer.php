<?php

class Wdc_Cartex_Block_Adminhtml_Grouped_Edit_Tab_Ajax_Prodserializer extends Mage_Core_Block_Template
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('catalog/product/edit/serializer.phtml');
        return $this;
    }

    public function getProductsJSON()
    {
        $result = array();
        if ($this->getProducts()) {
            foreach ($this->getProducts() as $iProductId) {
#                $id = $isEntityId ? $product->getEntityId() : $product->getId();
#                $result[$id] = $product->toArray(array('qty', 'position'));
                $result[$iProductId] = array('qty' => null, 'position' => 0);
            }
        }
        /*
        $result = array();
        if ($this->getProducts()) {
            $isEntityId = $this->getIsEntityId();
            foreach ($this->getProducts() as $product) {
                $id = $isEntityId ? $product->getEntityId() : $product->getId();
#                $result[$id] = $product->toArray(array('qty', 'position'));
                $result[$id] = array('qty' => null, 'position' => 0);
            }
        }
        */
        return $result ? Zend_Json_Encoder::encode($result) : '{}';
    }
}
