<?php

class Wdc_Cartex_Model_Mysql4_Promogroup_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    /**
     * Enter description here...
     *
     */
    protected function _construct()
    {
        $this->_init('abc/promogroup');
    }

    /**
     * Enter description here...
     *
     * @param Mage_Catalog_Model_Product|array|integer|null $product
     * @return Mage_Downloadable_Model_Mysql4_Promogroup_Collection
     */
    public function addProductToFilter($product)
    {
        if (empty($product)) {
            $this->addFieldToFilter('product_id', '');
        } elseif (is_array($product)) {
            $this->addFieldToFilter('product_id', array('in' => $product));
        } elseif ($product instanceof Mage_Catalog_Model_Product) {
            $this->addFieldToFilter('product_id', $product->getId());
        } else {
            $this->addFieldToFilter('product_id', $product);
        }

        return $this;
    }

    /**
     * Enter description here...
     *
     * @param integer $storeId
     * @return Mage_Downloadable_Model_Mysql4_Promogroup_Collection
     */
    public function addTitleToResult($storeId=0)
    {
        $this->getSelect()
            ->joinLeft(array('default_title_table' => $this->getTable('aitdownloadablefiles/aitfile_title')),
                '`default_title_table`.aitfile_id=`main_table`.aitfile_id AND `default_title_table`.store_id = 0',
                array('default_title' => 'title'))
            ->joinLeft(array('store_title_table' => $this->getTable('aitdownloadablefiles/aitfile_title')),
                '`store_title_table`.aitfile_id=`main_table`.aitfile_id AND `store_title_table`.store_id = ' . intval($storeId),
                array('store_title' => 'title','title' => new Zend_Db_Expr('IFNULL(`store_title_table`.title, `default_title_table`.title)')))
            ->order('main_table.sort_order ASC')
            ->order('title ASC');

        return $this;
    }

}
