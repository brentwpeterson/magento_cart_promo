<?php

class Wdc_Cartex_Adminhtml_CouponController extends Mage_Adminhtml_Controller_Action
{

	public function indexAction() {
		$this->loadLayout();
		$this->_setActiveMenu('promo/items');
		$this->_addBreadcrumb($this->__('Cart Promo'), $this->__('Cart Promo'));
		$this->_addContent($this->getLayout()->createBlock('cartex/adminhtml_coupon'));

		$this->renderLayout();
	}
	
	public function createAction()
	{
			$conum = $this->getRequest()->getParam('coupon_num');									
			$colen = $this->getRequest()->getParam('coupon_len');
			$use = $this->getRequest()->getParam('use');
			$custuse = $this->getRequest()->getParam('cust_use');
			$discount = $this->getRequest()->getParam('discount');
			$prex =  $this->getRequest()->getParam('code');
		$id = $this->getRequest()->getParam('id');
			
		if(!is_numeric($conum) || empty($conum))
		{
			$conum = 1;	
		}
			
		if(!is_numeric($colen) || empty($colen))
		{
			$colen = 3;	
		}
		
		if(!is_numeric($use) || empty($use))
		{
			$use = 0;	
		}
		
		if(!is_numeric($custuse) || empty($custuse))
		{
			$custuse = 0;	
		}
		
		if(!is_numeric($discount) || empty($discount))
		{
			$discount = 0;	
		}
			
		Mage::getModel('cartex/rules')->couponGenerator($this->getRequest()->getParam('id'), $conum, $colen, trim($prex), $use, $discount, $custuse);				
		
		echo '<h5>You created '.$conum.' coupon(s) </h5><a href="cartex/adminhtml_entity/edit/id/'.$id.'/">You need to refresh this screen</a>';
		
		//Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cartex')->__('You created '.$conum.' coupon(s)'));
		//$this->_redirect('*/*');	
	}
	
	public function updatecodesAction()
	{
		$postData = $this->getRequest()->getPost();
		
		$colen = $this->getRequest()->getParam('couponitems');
		
		print_r($colen);
		print_r($postData);	
	}
	
	protected function getCoupons()
	{
		$cartexId = Mage::app()->getFrontController()->getRequest()->get('id');	
		$collection = Mage::getModel('cartex/cart_coupon')->getCollection();		
		$collection->addFieldToFilter('cartex_id', array('in' =>array(0, 2)));	
		
		return $collection;	
	}
	
	public function testAction()
	{
		//$ruleModel = $model =  Mage::getModel('salesrule/rule')
		//	->load(231);
			
		//print_r($ruleModel);
		
		//Mage::getModel('cartex/rules')->couponDuplicator(231, 6, 6, 'fun');		
		
		$ruleModel =  Mage::getModel('salesrule/rule')->load(4485);	
		
		$newCoupon = Mage::getModel('salesrule/rule')->setData($ruleModel)->save();	
			
		$coup = Mage::getModel('salesrule/coupon')->loadPrimaryByRule($ruleModel);	
		
		
		Mage::getModel('salesrule/coupon')
		->setCode('new1llkkkk')
		->setRuleId($ruleModel->getId())
		->save();
		echo $coup->getId().'<br>';		
			print_r($ruleModel->getId());	
		
	}
	
	
		
			
		
	
	
    
}

?>