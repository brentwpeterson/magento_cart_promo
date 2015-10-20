<?php


class Wdc_Cartex_Adminhtml_QuoteController extends Mage_Adminhtml_Controller_Action
{
    protected function _initRule()
    {
        $this->_title($this->__('Promotions'))->_title($this->__('Shopping Cart Price Rules'));

        Mage::register('current_promo_quote_rule', Mage::getModel('salesrule/rule'));
        if ($id = (int) $this->getRequest()->getParam('id')) {
            Mage::registry('current_promo_quote_rule')
                ->load($id);
        }
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('promo/quote')
            ->_addBreadcrumb(Mage::helper('salesrule')->__('Promotions'), Mage::helper('salesrule')->__('Promotions'))
        ;
        return $this;
    }

    public function indexAction()
    {
       // $this->_title($this->__('Promotions'))->_title($this->__('Shopping Cart Price Rules'));

		$this->loadLayout();
		$this->_setActiveMenu('promo/items');
		$this->_addBreadcrumb($this->__('Cart Rules Duplicator'), $this->__('Cart Rules Duplicator'));
		$this->_addContent($this->getLayout()->createBlock('cartex/adminhtml_quote'));

		$this->renderLayout();

//        $this->_initAction()
//            ->_addBreadcrumb(Mage::helper('salesrule')->__('Catalog'), Mage::helper('salesrule')->__('Catalog'))
//            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
		$this->loadLayout();

		$this->_setActiveMenu('promo/items');
		$this->_addBreadcrumb($this->__('Cart Promo'), $this->__('Cart Promo'));
		$this->_addContent($this->getLayout()->createBlock('cartex/adminhtml_quote_edit'))			
			->_addLeft($this->getLayout()->createBlock('cartex/adminhtml_quote_edit_tabs'));
		$this->renderLayout();

    }

    /**
     * Promo quote save action
     *
     */
    public function saveAction()
    {
        set_time_limit(0);
        
        if ($this->getRequest()->getPost()) {
            try {
				$postData = $this->getRequest()->getPost();
				$conum = $postData['coupon_num'];									
				$colen = $postData['coupon_len'];					
				$prex =  $postData['code_prefix'];	
				$id = $this->getRequest()->getParam('id');
				
				if(!is_numeric($conum) || empty($conum))
				{
					$conum = 1;	
				}
				
				if(!is_numeric($colen) || empty($colen))
				{
					$colen = 3;	
				}			
						
				Mage::getModel('cartex/rules')->couponDuplicator($this->getRequest()->getParam('id'), $conum, $colen, trim($prex));				
				
				
				//echo '<h5>You created '.$conum.' coupon(s) </h5><a href="cartex/adminhtml_entity/edit/id/'.$id.'/">You need to refresh this screen</a>';
				
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cartex')->__('You created '.$conum.' coupon(s)'));
			 $this->_redirect('*/*');	

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('catalogrule')->__('An error occurred while saving the rule data. Please review the log and try again.'));
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                 $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('rule_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('salesrule/rule');
                $model->load($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('salesrule')->__('The rule has been deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('catalogrule')->__('An error occurred while deleting the rule. Please review the log and try again.'));
                Mage::logException($e);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('salesrule')->__('Unable to find a rule to delete.'));
        $this->_redirect('*/*/');
    }

    public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('salesrule/rule'))
            ->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    public function newActionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('salesrule/rule'))
            ->setPrefix('actions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    public function applyRulesAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->_initRule()->loadLayout()->renderLayout();
    }

    /**
     * Chooser source action
     */
    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $chooserBlock = $this->getLayout()->createBlock('adminhtml/promo_widget_chooser', '', array(
            'id' => $uniqId
        ));
        $this->getResponse()->setBody($chooserBlock->toHtml());
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('promo/quote');
    }
    
	/**
	* Export subscribers grid to CSV format
	*/
	public function exportCsvAction()
	{
		$fileName   = 'couponRequest_download.csv';
		$content    = $this->getLayout()->createBlock('Wdc_Cartex_Block_Adminhtml_Quote_Grid')->getCsv();

		$downloadIds = $this->getRequest()->getPost();
		
		$downloadIds = $downloadIds['download'];
		
		$this->_prepareDownloadResponse($fileName, $content);		
		
		if (!is_array($downloadIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cartex')->__('There was an error in updating the database, tell someone!'));
		}
		else {
			try {
									
			Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('adminhtml')->__(
							'Total of %d record(s) were successfully updated', count($downloadIds)
							)
						);
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}	
	}
}
