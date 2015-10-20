<?php

class Wdc_Cartex_Adminhtml_PromoController extends Mage_Adminhtml_Controller_action
{
 
    public function indexAction() {
		$this->loadLayout();
		$this->_setActiveMenu('promo/items');
		$this->_addBreadcrumb($this->__('Cart Promo'), $this->__('Cart Promo'));
		$this->_addContent($this->getLayout()->createBlock('cartex/adminhtml_promo'));

		$this->renderLayout();
    }
 
    public function editAction()
    {

		$this->loadLayout();

		$this->_setActiveMenu('promo/items');
		$this->_addBreadcrumb($this->__('Cart Product Promotion'), $this->__('Cart Promo'));
		$this->_addContent($this->getLayout()->createBlock('cartex/adminhtml_promo_edit'))			
			->_addLeft($this->getLayout()->createBlock('cartex/adminhtml_promo_edit_tabs'));
		$this->renderLayout();

    }
	
	public function getTemplate()
	{
		return Mage::registry('cartex_product');	
	}
	
	public function getProduct()
	{
		return Mage::registry('current_product');
	}
	
   
    public function newAction()
    {
        $this->_forward('edit');
    }
   
    public function saveAction()
    {		 
		
		 if ( $this->getRequest()->getPost() ) {
            try {
                $postData = $this->getRequest()->getPost();
                $cartexModel = Mage::getModel('cartex/cart_entity');
				
				
				$cartexId = Mage::getresourceModel('cartex/cart_entity')
					->fetchbyEntityId($this->getRequest()->getParam('id'));
			
				if($cartexId)
					{
					$cartexModel->setId($cartexId);	
					}
			
				$cartexModel
			          ->setPromoName($postData['promo_name'])
                    ->setDescription($postData['description'])
					->setPromoCode($postData['promo_code'])
					->setPromoType($postData['promo_type'])
//					->setToDate($postData['to_date'])
//					->setFromDate($postData['from_date'])
                    ->setIsActive($postData['is_active'])
					//->setItemLimit($itemLimit)
					->setStoreId($postData['store_id'])
					->setEntityId($this->getRequest()->getParam('id'))
					->setEntityTypeId(4)
					//->setRuleId($postData['rule_id'])
                    ->save();
					
//				if(isset($postData['rule_id']))
//					{				
//					$cartexModel->setRuleId($postData['rule_id'])->save();
//					}
					
		
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setCartexData(false);
 
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setCartexData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }
   
    public function deleteAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $cartexModel = Mage::getModel('cartex/cart_entity');
               
                $cartexModel->setId($this->getRequest()->getParam('id'))
                    ->delete();
                   
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }
	
	public function relatedAction()
	{
		$gridBlock = $this->getLayout()->createBlock('cartex/adminhtml_entity_edit_tab_related')
			->setGridUrl($this->getUrl('*/*/gridOnly', array('_current' => true, 'gridOnlyBlock' => 'related')));

		$cartexId = $this->getRequest()->getParam('id');

		$cartexProducts = Mage::getResourceModel('cartex/cart_groups');
		$productsArray = $cartexProducts->fetchbyAttributeId($cartexId);

		$serializerBlock = $this->_createSerializerBlock('links[related]', $gridBlock, $productsArray);
		$this->_outputBlocks($gridBlock, $serializerBlock);
	}  
	
	public function optionsAction()
	{
		$this->_initProduct();

		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_options', 'admin.product.options')->toHtml()
			);
	}
	
	public function addedAction()
	{
		$gridBlock = $this->getLayout()->createBlock('cartex/adminhtml_entity_edit_tab_added')
			->setGridUrl($this->getUrl('*/*/gridOnly', array('_current' => true, 'gridOnlyBlock' => 'added')));

		$cartexId = $this->getRequest()->getParam('id');

		$cartexProducts = Mage::getResourceModel('cartex/cart_item');
		$productsArray = $cartexProducts->fetchbyAttributeId($cartexId);

		$serializerBlock = $this->_createSerializerBlock('links[added]', $gridBlock, $productsArray);
		$this->_outputBlocks($gridBlock, $serializerBlock);
	}    
	
	public function productsAction()
	{
		$gridBlock = $this->getLayout()->createBlock('cartex/adminhtml_entity_edit_tab_products')
			->setGridUrl($this->getUrl('*/*/gridOnly', array('_current' => true, 'gridOnlyBlock' => 'products')));

		$cartexId = $this->getRequest()->getParam('id');

		$cartexProducts = Mage::getResourceModel('cartex/cart_products');
		$productsArray = $cartexProducts->fetchbyAttributeId($cartexId);

		$serializerBlock = $this->_createSerializerBlock('links[products]', $gridBlock, $productsArray);
		$this->_outputBlocks($gridBlock, $serializerBlock);
	} 
	
	public function productstabAction()
	{
		$gridBlock = $this->getLayout()->createBlock('cartex/adminhtml_entity_edit_tab_productstab')
			->setGridUrl($this->getUrl('cartex/adminhtml_entity/gridOnly', array('_current' => true, 'gridOnlyBlock' => 'productstab')));

		$cartexId = $this->getRequest()->getParam('id');

		$cartexProducts = Mage::getResourceModel('cartex/cart_products');
		$productsArray = $cartexProducts->fetchbyParentId($cartexId);
		//$productsArray = $cartexProducts->fetchbyAttributeId($cartexId);

		$serializerBlock = $this->_createSerializerBlock('links[productstab]', $gridBlock, $productsArray);
		$this->_outputBlocks($gridBlock, $serializerBlock);
	} 


	/**
	 * Get specified tab grid
	 */
	public function gridOnlyAction()
	{
		$this->loadLayout();
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('cartex/adminhtml_entity_edit_tab_' . $this->getRequest()->getParam('gridOnlyBlock'))
			->toHtml()
			);
	}   
	
	
	 
	
	protected function _createSerializerBlock($inputName, Mage_Adminhtml_Block_Widget_Grid $gridBlock, $productsArray)
	{
		return $this->getLayout()->createBlock('cartex/adminhtml_entity_edit_tab_ajax_serializer')
		->setGridBlock($gridBlock)
		->setProducts($productsArray)
		->setInputElementName($inputName)
		;
	}    
	
	/**
	 * Output specified blocks as a text list
	 */
	protected function _outputBlocks()
	{
		$blocks = func_get_args();
		$output = $this->getLayout()->createBlock('adminhtml/text_list');
		foreach ($blocks as $block) {
			$output->insert($block, '', true);
		}
		$this->getResponse()->setBody($output->toHtml());
} 
}


?>