<?php

class Wdc_Cartex_Adminhtml_EntityController extends Mage_Adminhtml_Controller_Action
{

    public function preDispatch()
    {
        parent::preDispatch();
    }
    
    protected function _initAction()
    {
		$this->loadLayout()
			->_setActiveMenu('promo/cartex')
			->_addBreadcrumb(Mage::helper('cartex')->__('Rules Rule'), Mage::helper('cartex')->__('Rules Rule'));
        return $this;
    }  
    
	public function indexAction()
	{
		$this->_initAction();
			//->_addContent($this->getLayout()->createBlock('cartex/adminhtml_entity_grid'));
		$this->renderLayout();
	}
	
    public function editAction()
    {
		$id = $this->getRequest()->getParam('cartex_id');
        $model = Mage::getModel('cartex/cart_entity');

        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cartex')->__('This template no longer exists'));
                $this->_redirect('*/*');
                return;
            }
        }
        
        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        Mage::register('rules_data', $model);
       
		$block = $this->getLayout()->createBlock('cartex/adminhtml_entity_edit');
            //->setData('action', $this->getUrl('*/*/save'));

        $this->_initAction();
        $this->getLayout()->getBlock('head')
            ->setCanLoadExtJs(true)
            ->setCanLoadRulesJs(true);

        $this
            ->_addBreadcrumb($id ? Mage::helper('cartex')->__('Edit Rule') : Mage::helper('cartex')->__('New Rule'), $id ? Mage::helper('cartex')->__('Edit Rule') : Mage::helper('cartex')->__('New Rule'))
            ->_addContent($block)
			->_addLeft($this->getLayout()->createBlock('cartex/adminhtml_entity_edit_tabs'))
            ->renderLayout();

    }
 
	public function newAction() {
		$this->_forward('edit');
		//$this->_forward('tt');
	}
	
	public function saveAction() {

		
		if ($data = $this->getRequest()->getPost()) {
		    
            $model = Mage::getModel('cartex/cart_entity');

			if ($id = $this->getRequest()->getParam('cartex_id')) {
                $model->load($id);
                if ($id != $model->getId()) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cartex')->__('The page you are trying to save no longer exists'));
                    Mage::getSingleton('adminhtml/session')->setPageData($data);
					$this->_redirect('*/*/edit', array('cartex_id' => $this->getRequest()->getParam('cartex_id')));
                    return;
                }
            }

            $model->setData($data);

            Mage::getSingleton('adminhtml/session')->setPageData($model->getData());
            try {
                $model->save();
                
                $bHasRequired = 0;
                
                $option2tpl = Mage::getResourceModel('cartex/aitoption2tpl');
                
                // saving options
                $options = Mage::getModel('catalog/product_option');
                
                $aOptionIds = array();
                if (isset($data['product']) AND is_array($data['product']['options']) and !empty($data['product']['options']))
                {
                    foreach ($data['product']['options'] as $aOption)
                    {
                        if ($aOption['is_require'])
                        {
                            $bHasRequired = 1;
                        }
                    }
                    
                    $aOptionIds = $options->saveRuleOptions($data['product']['options']);
                    if (!empty($aOptionIds))
                    {
                    	$option2tpl->clearRuleOptions($model->getId());
                    	
                    	foreach ($aOptionIds as $iOptionId)
                    	{
	                    	$option2tpl->addRelationship($model->getId(), $iOptionId);
                    	}
                    }
                }
                else 
                {
                    $bHasRequired = $option2tpl->checkProductHasRequiredRuleOptions($model->getId());
                }
                
                if (!$data['is_active'])
                {
                    $bHasRequired = 0;
                }
                
                $model->addData(array('required_options' => $bHasRequired));
                
                $model->save();
                
            	$product2tpl = Mage::getResourceModel('cartex/aitproduct2tpl');
            	
            	$aProductHash = $product2tpl->getRuleProducts($model->getId());
            	
                if (isset($data['links']['related']))
                {
                	
                	if ($aProductHash)
                	{
                	    foreach ($aProductHash as $iProductId)
                	    {
                	        $aProductOldHash[$iProductId] = $iProductId;
                	    }
                	}
                	else 
                	{
                	    $aProductOldHash = array();
                	}
                	
                	$aProductSaveHash = array();
                	
                	if ($data['links']['related'])
                	{
                	    $aRelatedData = explode('&', $data['links']['related']);
                	    
                	    foreach ($aRelatedData as $sItem)
                	    {
                	        $iProductId = substr($sItem, 0, strpos($sItem, '='));
                	        
                	        if (isset($aProductOldHash[$iProductId]))
                	        {
                	            unset($aProductOldHash[$iProductId]);
                	        }
                	        else 
                	        {
                    	        $aProductSaveHash[$iProductId] = $iProductId;
                	        }
                	    }
                	}
                	
                	if ($aProductSaveHash) // to add new
                	{
                	    foreach ($aProductSaveHash as $iProductId)
                	    {
                	        $aData = array('cartex_id' => $model->getId(), 'sort_order' => 0);
                	        $product2tpl->addRelationship($iProductId, $aData);
                	    }
                	}
                	
                	if ($aProductOldHash) // to delete old
                	{
                    	$product2tpl->clearRuleProducts($model->getId(), $aProductOldHash);
                	}
                	
                }
               
                $product2required = Mage::getResourceModel('cartex/aitproduct2required');
                
            	$product2required->setRuleHasRequiredOptions($model->getId(), $bHasRequired, $aProductHash);
                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cartex')->__('Rule was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setPageData(false);
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                $this->_redirect('*/*/edit', array('cartex_id' => $this->getRequest()->getParam('cartex_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
	}
 
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('cartex_id')) {
            try {
                $model = Mage::getModel('cartex/aittemplate');
                $model->load($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cartex')->__('Rule was successfully deleted'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cartex')->__('Unable to find a page to delete'));
        $this->_redirect('*/*/');
    }

    
    /**
     * Get connected with template products grid and serializer block
     */
  


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