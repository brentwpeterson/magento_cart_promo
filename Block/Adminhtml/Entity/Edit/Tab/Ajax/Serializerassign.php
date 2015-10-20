<?php

class Wdc_Cartex_Block_Adminhtml_Entity_Edit_Tab_Ajax_Serializerassign extends Mage_Core_Block_Template
{
//    public function _construct()
//    {
//        parent::_construct();
//       // $this->setTemplate('catalog/product/edit/serializer.phtml');
//       // return $this;
//		
//    }

	public function _toHtml()
	{
		
		$_id = 'id_' . md5(microtime());
		$html =	'<input type="hidden" name="'.$this->getInputElementName().'"  value="" id="'.$_id.'" />
		<script type="text/javascript">
		// create serializer controller, that will syncronize grid checkboxes with hidden input
		new productLinksController(\''.$_id.'\', '.$this->getassignProductsJSON().', '.$this->getGridBlock()->getJsObjectName().');
		</script>';
		
		return $html;
	}

	public function getassignProductsJSON()
    {
		////Mage::helper('errorlog')->insert('getassignProductsJSON()', 'madeithere');
        $result = array();
        if ($this->getAssignedProducts()) {
			
			////Mage::helper('errorlog')->insert('getassignProductsJSON()', 'intoif');
			foreach ($this->getAssignedProducts() as $iProductId) {
				
				
                $result[$iProductId] = array('qty' => null, 'position' => 0);
            }
        }

        return $result ? Zend_Json_Encoder::encode($result) : '{}';
    }
    
    protected function getAssignedProducts()
    {
		
		
		$products = array();
		$productEntityTypeId = Mage::getModel('eav/entity_type')->loadByCode('catalog_product')->getId();
		$cartexId = Mage::app()->getFrontController()->getRequest()->get('id');	
		
		$collection = Mage::getresourceModel('cartex/cart_groups_collection')
			->addFilter('entity_type_id', $productEntityTypeId)
			->addFilter('wdc_attribute_id', $cartexId);
			
			foreach ($collection as $item)
			{
				
			////Mage::helper('errorlog')->insert('getAssignedProducts()-collection', $item->getEntityId());
			$products[] = $item->getEntityId();
			////Mage::helper('errorlog')->insert('serializer', $item->getEntityId());
			}
			
			return $products;
			
	}
	

}
