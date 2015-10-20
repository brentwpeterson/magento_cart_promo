<?php

class Wdc_Cartex_Block_Adminhtml_Entity_Edit_Tab_Ajax_Catserializer extends Mage_Core_Block_Template
{

	protected $_cats;
	
	protected function _toHtml()
	{
		$_id = 'id_' . md5(microtime());
		$serialize = '<input type="hidden" name="'.$this->getInputElementName().'"  value="" id="'.$_id.'" />';
		$serialize.= '<script type="text/javascript">';
		$serialize.= 'new productLinksController(\''.$_id.'\','.$this->getCatsJSON().','.$this->getGridBlock()->getJsObjectName().');';
		$serialize.= '</script>';
		return $serialize;
	}

  public function getCatsJSON()
    {
        $result = array();
        if ($this->getCats()) {
			foreach ($this->getProdducts() as $iFaqId) {
               $result[$iFaqId] = array('qty' => null, 'position' => 0);
            }
        }
        return $result ? Zend_Json_Encoder::encode($result) : '{}';
    }
	
	public function getCats()
	{
		if (is_null($this->_cats)) {
			$catIds = $this->getcatIds();

			if(!is_array($catIds)) {
				$catIds = array(0);
			}
			$this->_cats = $this-> getCategoryCollection()			
				->addIdFilter($catIds);
		}

		return $this->_cats;
	}
	
	protected function getCategoryCollection()
	{
		$storeId = $this->getRequest()->getParam('store', $this->_getDefaultStoreId());
		$collection = $this->getData('category_collection');
		if (is_null($collection)) {
			$collection = Mage::getModel('catalog/category')->getCollection();
			$collection->addAttributeToSelect('name')
				->addAttributeToSelect('is_active')
				->setProductStoreId($storeId)			
				->setStoreId($storeId);

			$this->setData('category_collection', $collection);
		}
		return $collection;
	}
	
	
	
	protected function _getDefaultStoreId()
	{
		return Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;
	}
	
	public function getCatIds()
	{
		$session = Mage::getSingleton('adminhtml/session');

		if ($this->_getRequest()->isPost() && $this->_getRequest()->getActionName()=='edit') {
			//$session->setFaqIds($this->_getRequest()->getParam('faq', null));
			$session->setFaqIds(1,2,3);
		}
		return $session->getFaqIds();
	}
}
