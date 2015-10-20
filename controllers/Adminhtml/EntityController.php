<?php

class Wdc_Cartex_Adminhtml_EntityController extends Mage_Adminhtml_Controller_action
{
 
    public function indexAction() {
		$this->loadLayout();
		$this->_setActiveMenu('promo/items');
		$this->_addBreadcrumb($this->__('Cart Promo'), $this->__('Cart Promo'));
		$this->_addContent($this->getLayout()->createBlock('cartex/adminhtml_entity'));

		$this->renderLayout();
    }
 
    public function editAction()
    {

		$this->loadLayout();

		$this->_setActiveMenu('promo/items');
		$this->_addBreadcrumb($this->__('Cart Promo'), $this->__('Cart Promo'));
		$this->_addContent($this->getLayout()->createBlock('cartex/adminhtml_entity_edit'))
	
		->_addLeft($this->getLayout()->createBlock('cartex/adminhtml_entity_edit_tabs'));
		$this->renderLayout();

    }
	
   
    public function newAction()
    {
        $this->_forward('edit');
    }
   
    public function tsaveAction()
	{
		$postData = $this->getRequest()->getPost();
		print_r($postData['page']);
	
	}
	
    public function saveAction()
    {
        if ( $this->getRequest()->getPost() ) {
            try {
                $postData = $this->getRequest()->getPost();
                $cartexModel = Mage::getModel('cartex/cart_entity');
				
				if(isset($postData['item_limit']))
					{
					if($postData['item_limit'] < 1)
						{
						$itemLimit = 1;
						}
					else
						{
						$itemLimit = $postData['item_limit'];
						}
					}   		
			
                $cartexModel->setId($this->getRequest()->getParam('id'))
				//->setData($postData)
                    ->setPromoName($postData['promo_name'])
                    ->setDescription($postData['description'])
					//->setPromoCode($postData['promo_code'])
					->setPromoType($postData['promo_type'])
//					->setToDate($postData['to_date'])
//					->setFromDate($postData['from_date'])
                    ->setIsActive($postData['is_active'])
//					->setUseRules($postData['use_rules'])
//					->setItemLimit($itemLimit)
					->setStoreId($postData['store_id'])
					//->setRuleId($postData['rule_id'])
                    ->save();
					
				if(count($this->getCoupons()) > 0)
				{
					foreach ($this->getCoupons() as $couponItem){
						
						if(isset($postData['coupon_'.$couponItem->getCouponId()])){					
							Mage::getModel('cartex/rules')->updateCoupon($couponItem->getCouponId(), $couponItem->getRuleId(), $postData['coupon_'.$couponItem->getCouponId()]);
						}					
					}	
				}
				
				
				
				if(isset($postData['rule_id']))
					{				
					$cartexModel->setRuleId($postData['rule_id'])->save();
					}
					
					
				
				if(isset($postData['discount_amount']))
				{				
					$cartexModel->setDiscountAmount($postData['discount_amount'])->save();
				}
									
				if(isset($postData['exception_type_id']))
				{				
					$cartexModel->setExceptionTypeId($postData['exception_type_id'])->save();
				}
					
				if(isset($postData['qual_statement']))
					{
										
					if(!Mage::getresourceModel('cartex/cart_value')->checkValueExist($cartexModel->getId()))
					{
						$valueModel = Mage::getModel('cartex/cart_value');
						$valueModel->setWdcAttributeId($cartexModel->getId());					
					}
					else
					{
						$valueId = Mage::getresourceModel('cartex/cart_value')->fetchbyAttributeId($cartexModel->getId());	
						$valueModel = Mage::getModel('cartex/cart_value')->load($valueId);
					}
					
					$valueModel->setQualStatement($postData['qual_statement']);
					$valueModel->setMinVal($postData['min_val']);
					$valueModel->setMaxVal($postData['max_val']);
					$valueModel->save();
					}
					
					
					/**Add Related Products **/
				//if (isset($postData['assignproducts']))
				if (isset($postData['links']['related']))
				{						
					$cartexId = $cartexModel->getId();
					//$this->_assignProducts($postData, $cartexModel->getId());
					$productEntityTypeId = Mage::getModel('eav/entity_type')->loadByCode('catalog_product')->getId();		
					//$aRelatedData = $postData['assignproducts'];
					$aRelatedData = explode('&', $postData['links']['related']);
					$postProducts[] = array();
					foreach ($aRelatedData as $sItem)
					{							
						$iProductId = substr($sItem, 0, strpos($sItem, '='));
						 //$iProductId; changed		
						 
						////Mage::helper('errorlog')->insert('posted Products', $iProductId);	
						if(!empty($iProductId)){ // changed back$sItem)){ //$iProductId)){ CHANGED							 
							
							$postProducts[] =  $iProductId; //change $sItem to $iProductId
							if(!Mage::getResourceModel('cartex/cart_groups')->checkProductExist($cartexId, $iProductId, $productEntityTypeId)) //change $sItem to $iProductId
							{																			
								
								////Mage::helper('errorlog')->insert('post', $sItem);
								
								$groupModel = Mage::getModel('cartex/cart_groups');
								$groupModel
									->setWdcAttributeId($cartexId)
									->setEntityId($iProductId) //change $sItem to $iProductId
									->setEntityTypeId($productEntityTypeId)
									->save();
							}								
						}	
					}
					
					$collection = Mage::getresourceModel('cartex/cart_groups_collection')
						->addFilter('entity_type_id', $productEntityTypeId)
						->addFilter('wdc_attribute_id', $cartexId);
					
					if(isset($data['in_products'])){
						$filter = $data['in_products'];
					}		
					$assignedIds = Mage::getresourceModel('cartex/cart_groups')->fetchbyAttributeId($cartexId, $productEntityTypeId);
					if($filter == '0'){
						$collection->addFieldToFilter('entity_id', array('nin'=>$assignedIds));					
					}
					elseif($filter == '1'){
						$collection->addFieldToFilter('entity_id', array('in'=>$assignedIds));	
					}
					$collection->setPageSize($data['limit']);
					$collection->setCurPage($data['page']);
					
					$inProducts = array();
					foreach ($collection as $thingy)
					{			
						$inProducts[] = $thingy->getEntityId();	
					}	
					
					if($inProducts){
						foreach ($inProducts as $inProductId)
						{
							if(!in_array($inProductId, $postProducts)) //$inProducts))
							{
								
								Mage::getResourceModel('cartex/cart_groups')->deletebyEntityId($cartexId, $inProductId, $productEntityTypeId);										
							}
						}
					}	
					
				}
				/** end Add related products**/					

				
				/**Add Added Products **/
				if (isset($postData['addproducts']))
				{
					// All posted data
					$aRelatedData =  $postData['addproducts'];
					
					//print_r($postData['links']['related']);
					
					$postProducts[] = array();
					foreach ($aRelatedData as $sItem)
					{							
						//posted item id
						$iProductId = $sItem; //substr($sItem, 0, strpos($sItem, '='));
						
						$postProducts[] = $iProductId;
						
						if(!empty($iProductId)){							
							
							if(!Mage::getResourceModel('cartex/cart_item')->checkProductExist($cartexModel->getId(), $iProductId))
							{																			
								$groupModel = Mage::getModel('cartex/cart_item');
								$groupModel->setWdcAttributeId($cartexModel->getId());
								$groupModel->setEntityId($iProductId);
								$groupModel->save();
							}
							
						}								
					}
					
					$inProducts = Mage::getresourceModel('cartex/cart_item')->fetchbyAttributeId($cartexModel->getId());
					if($inProducts){
						foreach ($inProducts as $inProductId)
						{
							if(!in_array($inProductId, $postProducts))
							{
								Mage::getResourceModel('cartex/cart_item')->deletebyEntityId($cartexModel->getId(), $inProductId);										
							}
						}
					}
					
					
				}
				/** end Add Added products**/	
				
				/**Add Grouped Products **/
				if (isset($postData['links']['products']))
				{
					// All posted data
					$aRelatedData = explode('&', $postData['links']['products']);
					
					//print_r($postData['links']['related']);
					
					$postProducts[] = array();
					foreach ($aRelatedData as $sItem)
					{							
						//posted item id
						
						
						$iProductId = substr($sItem, 0, strpos($sItem, '='));
						
						$postProducts[] = $iProductId;
						
						if(!empty($iProductId)){							
							
							if(!Mage::getResourceModel('cartex/cart_products')->checkProductExist($cartexModel->getId(), $iProductId))
							{																			
								$groupModel = Mage::getModel('cartex/cart_products');
								$groupModel->setWdcAttributeId($cartexModel->getId());
								$groupModel->setEntityId($iProductId);
								$groupModel->save();
							}
							
						}								
					}
					
					$inProducts = Mage::getresourceModel('cartex/cart_products')->fetchbyAttributeId($cartexModel->getId());
					if($inProducts){
						foreach ($inProducts as $inProductId)
						{
							if(!in_array($inProductId, $postProducts))
							{
								
								Mage::getResourceModel('cartex/cart_products')->deletebyEntityId($cartexModel->getId(), $inProductId);										
							}
						}
					}
					
					
				}
				/** end Add Added products**/
				
				
				/** Start Add Category CARTEX **/
				
				if (isset($postData['items']))
				{		
					
					$categoryEntityTypeId = Mage::getModel('eav/entity_type')->loadByCode('catalog_category')->getId();
					$catRelatedData = $postData['items'];
					
					foreach ($catRelatedData as $sItem)
					{							
						$postCats[] = $sItem;
						
						if(!empty($sItem)){
								
							if(!Mage::getResourceModel('cartex/cart_groups')->checkProductExist($cartexModel->getId(), $sItem, $categoryEntityTypeId))
								{																			
									$groupModel = Mage::getModel('cartex/cart_groups');
									$groupModel->setWdcAttributeId($cartexModel->getId());
									$groupModel->setEntityId($sItem);
									$groupModel->setEntityTypeId($categoryEntityTypeId);
									$groupModel->save();
								}
						}												
					}	
					
					$inGroups = Mage::getresourceModel('cartex/cart_groups')->fetchbyAttributeId($cartexModel->getId(), $categoryEntityTypeId);
					if($inGroups){
						foreach ($inGroups as $inCatId)
						{
							if(!in_array($inCatId, $postCats))
							{
								Mage::getResourceModel('cartex/cart_groups')->deletebyEntityId($cartexModel->getId(), $inCatId, $categoryEntityTypeId);										
							}
						}
					}
				}
				
				
				/** End ADD CARTEGORY CARTEX **/
				
				
				
				
				/** Start Add Attribute CARTEX **/
				
				if (isset($postData['attributeitems']))
				{		
					
				//	$categoryEntityTypeId = Mage::getModel('eav/entity_type')->loadByCode('catalog_category')->getId();
					$attRelatedData = $postData['attributeitems'];
					
					foreach ($attRelatedData as $sItem)
					{							
						$postAtts[] = $sItem;
						
						if(!empty($sItem)){
							
							
							//checkAttributeSetExist($wdcAttributeId, $attributeSetId, $entityTypeId=0)
							if(!Mage::getResourceModel('cartex/cart_groups')->checkAttributeSetExist($cartexModel->getId(), $sItem, 0))
							{																			
								$groupModel = Mage::getModel('cartex/cart_groups');
								$groupModel->setWdcAttributeId($cartexModel->getId());
								$groupModel->setAttributeSetId($sItem);
								//$groupModel->setEntityTypeId($categoryEntityTypeId);
								$groupModel->save();
							}
						}												
					}	
					
					$inAttributeGrp = Mage::getresourceModel('cartex/cart_groups')->fetchAttributeSetbyCartexId($cartexModel->getId());
					if($inAttributeGrp){
						foreach ($inAttributeGrp as $inAttId)
						{
							if(!in_array($inAttId, $postAtts))
							{
								Mage::getResourceModel('cartex/cart_groups')->deletebyAttributeSetId($cartexModel->getId(), $inAttId);										
							}
						}
					}
				}
				
				
				/** End ADD CARTEGORY CARTEX **/
				
				/** Start Add Coupons CARTEX **/
				
				if (isset($postData['couponitems']))
				{		
					$couponData = $postData['couponitems'];
													
					foreach ($couponData as $sItem)
					{							
						$postCpns[] = $sItem;
						
						if(!empty($sItem)){
						
							if(!Mage::getResourceModel('cartex/cart_coupon')->checkCouponExist($cartexModel->getId(), $sItem))
							{	
								$couponModel = Mage::getModel('cartex/cart_coupon')->load($sItem);
								$couponModel->setCartexId($cartexModel->getId());								
								$couponModel->save();
							}
						}												
					}	
					
					$inAttributeCpn = Mage::getresourceModel('cartex/cart_coupon')->fetchbyCartexId($cartexModel->getId());
									
					if($inAttributeCpn){
						foreach ($inAttributeCpn as $inCpnId)
						{
							if(!in_array($inCpnId, $postCpns))
							{
								$couponModel = Mage::getModel('cartex/cart_coupon')->load($inCpnId);
								$couponModel->setCartexId(0);														
								$couponModel->save();	
								
								////Mage::helper('errorlog')->insert('coupons', $postData['couponcodes']);								
							}
						}
					}					
				}				
				
				/** End Coupons CARTEX **/
               
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setCartexData(false);
 
				$thisid = $this->getRequest()->getParam('id');
               // $this->_redirect('*/*/');
				if(isset($thisid)){
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
            else{
				
				$this->_redirect('*/*/');
                return;
			}
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setCartexData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
		$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
        
    }
    
	protected function _assignProducts($data, $cartexId)
    {		
		$productEntityTypeId = Mage::getModel('eav/entity_type')->loadByCode('catalog_product')->getId();		
		$aRelatedData = $data;		
		$postProducts[] = array();
		foreach ($aRelatedData as $sItem)
		{							
			$postProducts[] =  $sItem; //$iProductId; changed			
			if(!empty($sItem)){ //$iProductId)){ CHANGED							 
				
				if(!Mage::getResourceModel('cartex/cart_groups')->checkProductExist($cartexId, $sItem, $productEntityTypeId))
				{																			
					$groupModel = Mage::getModel('cartex/cart_groups');
					$groupModel
						->setWdcAttributeId($cartexId)
						->setEntityId($sItem)
						->setEntityTypeId($productEntityTypeId)
						->save();
				}								
			}	
		}
		
		$collection = Mage::getresourceModel('cartex/cart_groups_collection')
			->addFilter('entity_type_id', $productEntityTypeId)
			->addFilter('wdc_attribute_id', $cartexId);
		
		if(isset($data['in_products'])){
			$filter = $data['in_products'];
		}		
		$assignedIds = Mage::getresourceModel('cartex/cart_groups')->fetchbyAttributeId($cartexId, $productEntityTypeId);
		if($filter == '0'){
			$collection->addFieldToFilter('entity_id', array('nin'=>$assignedIds));					
		}
		elseif($filter == '1'){
			$collection->addFieldToFilter('entity_id', array('in'=>$assignedIds));	
		}
		$collection->setPageSize($data['limit']);
		$collection->setCurPage($data['page']);
		
		$inProducts = array();
		foreach ($collection as $thingy)
		{			
			$inProducts[] = $thingy->getEntityId();	
		}	
				
		if($inProducts){
			foreach ($inProducts as $inProductId)
			{
				if(!in_array($inProductId, $postProducts)) //$inProducts))
				{
					Mage::getResourceModel('cartex/cart_groups')->deletebyEntityId($cartexId, $inProductId, $productEntityTypeId);										
				}
			}
		}	
		
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
		$productEntityTypeId = Mage::getModel('eav/entity_type')->loadByCode('catalog_product')->getId();

		$collection = Mage::getresourceModel('cartex/cart_groups_collection')
			->addFilter('entity_type_id', $productEntityTypeId)
			->addFilter('wdc_attribute_id', $cartexId);
		
		
		$productsArray = array();
		foreach ($collection as $product)
		{
			$productsArray[] = $product->getEntityId();	
		}

		$serializerBlock = $this->_createSerializerBlock('links[related]', $gridBlock, $productsArray);
		$this->_outputBlocks($gridBlock, $serializerBlock);
	}  
	
	public function couponsAction()
	{
		$gridBlock = $this->getLayout()->createBlock('cartex/adminhtml_entity_edit_tab_coupons')
			->setGridUrl($this->getUrl('*/*/gridOnly', array('_current' => true, 'gridOnlyBlock' => 'coupons')));

		$cartexId = $this->getRequest()->getParam('id');	
		$cartexCoupons = Mage::getResourceModel('cartex/cart_coupon');
		$coupons = $cartexCoupons->fetchbyCartexId($cartexId);

		$serializerBlock = $this->_createSerializerBlock('links[coupons]', $gridBlock, $coupons);
		$this->_outputBlocks($gridBlock, $serializerBlock);
	}
	
	public function categoriesAction()
	{
		$gridBlock = $this->getLayout()->createBlock('cartex/adminhtml_entity_edit_tab_categories')
			->setGridUrl($this->getUrl('*/*/gridOnly', array('_current' => true, 'gridOnlyBlock' => 'categories')));

		$cartexId = $this->getRequest()->getParam('id');

		$cartexProducts = Mage::getResourceModel('cartex/cart_groups');
		$productsArray = $cartexProducts->fetchbyAttributeId($cartexId);

		$serializerBlock = $this->_createSerializerBlock('links[categories]', $gridBlock, $productsArray);
		$this->_outputBlocks($gridBlock, $serializerBlock);
	}  
	
	public function attributesAction()
	{
		$gridBlock = $this->getLayout()->createBlock('cartex/adminhtml_entity_edit_tab_attributesets')
			->setGridUrl($this->getUrl('*/*/gridOnly', array('_current' => true, 'gridOnlyBlock' => 'attributesets')));

		$cartexId = $this->getRequest()->getParam('id');

		$cartexProducts = Mage::getResourceModel('cartex/cart_groups');
		$productsArray = $cartexProducts->fetchbyAttributeId($cartexId);

		$serializerBlock = $this->_createSerializerBlock('links[attributes]', $gridBlock, $productsArray);
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
		
	
		//Mage::helper('errorlog')->insert('serial', $inputName);	
		
		
		
		return $this->getLayout()->createBlock('cartex/adminhtml_entity_edit_tab_ajax_serializer')
		->setGridBlock($gridBlock)		
		->setProducts($productsArray)
		->setInputElementName($inputName)
		;
	} 
	
	 
	
/*	protected function _createSerializerBlock($inputName, Mage_Adminhtml_Block_Widget_Grid $gridBlock, $productsArray, $append=null)
	{
		if($append)
		{
			
			$block = 'cartex/adminhtml_entity_edit_tab_ajax_serializer'.$append;
		}
		else
		{
			$block = 'cartex/adminhtml_entity_edit_tab_ajax_serializer';	
		}
		
		
		
		return $this->getLayout()->createBlock($block)
		->setGridBlock($gridBlock)
		->setProducts($productsArray)
		->setInputElementName($inputName)
		;
	} */   
	
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

	protected function getCoupons()
	{
		$cartexId = Mage::app()->getFrontController()->getRequest()->get('id');	
		$collection = Mage::getModel('cartex/cart_coupon')->getCollection();		
		$collection->addFieldToFilter('cartex_id', array('in' =>array(0, $this->getRequest()->getParam('id'))));	
		
		return $collection;		
	}
}


?>