<?php

class Wdc_Cartex_Adminhtml_GroupedController extends Mage_Adminhtml_Controller_action
{

	protected function _construct()
	{
		// Define module dependent translate
		$this->setUsedModuleName('Mage_Catalog');
	}

	/**
	 * Initialize product from request parameters
	 *
	 * @return Mage_Catalog_Model_Product
	 */
	protected function _initProduct()
	{
		$productId  = (int) $this->getRequest()->getParam('id');
		$product    = Mage::getModel('catalog/product')
			->setStoreId($this->getRequest()->getParam('store', 0));

		if (!$productId) {
			if ($setId = (int) $this->getRequest()->getParam('set')) {
				$product->setAttributeSetId($setId);
			}

			if ($typeId = $this->getRequest()->getParam('type')) {
				$product->setTypeId($typeId);
			}
		}

		if ($productId) {
			$product->load($productId);
		}

		$attributes = $this->getRequest()->getParam('attributes');
		if ($attributes && $product->isConfigurable() &&
			(!$productId || !$product->getTypeInstance()->getUsedProductAttributeIds())) {
				$product->getTypeInstance()->setUsedProductAttributeIds(
					explode(",", base64_decode(urldecode($attributes)))
					);
			}

			// Init attribute label names for store selected in dropdown
			Mage_Catalog_Model_Resource_Eav_Attribute::initLabels($product->getStoreId());

			// Required attributes of simple product for configurable creation
			if ($this->getRequest()->getParam('popup')
				&& $requiredAttributes = $this->getRequest()->getParam('required')) {
				$requiredAttributes = explode(",", $requiredAttributes);
				foreach ($product->getAttributes() as $attribute) {
					if (in_array($attribute->getId(), $requiredAttributes)) {
						$attribute->setIsRequired(1);
					}
				}
			}

			if ($this->getRequest()->getParam('popup')
				&& $this->getRequest()->getParam('product')
				&& !is_array($this->getRequest()->getParam('product'))
				&& $this->getRequest()->getParam('id', false) === false) {

				$configProduct = Mage::getModel('catalog/product')
				->setStoreId(0)
				->load($this->getRequest()->getParam('product'))
				->setTypeId($this->getRequest()->getParam('type'));

				/* @var $configProduct Mage_Catalog_Model_Product */
				$data = array();
				foreach ($configProduct->getTypeInstance()->getEditableAttributes() as $attribute) {

					/* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
					if(!$attribute->getIsUnique()
						&& $attribute->getFrontend()->getInputType()!='gallery'
						&& $attribute->getAttributeCode() != 'required_options'
						&& $attribute->getAttributeCode() != 'has_options'
						&& $attribute->getAttributeCode() != $configProduct->getIdFieldName()) {
						$data[$attribute->getAttributeCode()] = $configProduct->getData($attribute->getAttributeCode());
					}
				}

				$product->addData($data)
				->setWebsiteIds($configProduct->getWebsiteIds());
			}

			$product->setData('_edit_mode', true);

			Mage::register('product', $product);
			Mage::register('current_product', $product);
			return $product;
		}

		/**
		 * Create serializer block for a grid
		 *
		 * @param string $inputName
		 * @param Mage_Adminhtml_Block_Widget_Grid $gridBlock
		 * @param array $productsArray
		 * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Ajax_Serializer
		 */
		protected function _createSerializerBlock($inputName, Mage_Adminhtml_Block_Widget_Grid $gridBlock, $productsArray)
		{
			return $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_ajax_serializer')
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

		/**
		 * Product list page
		 */
		public function indexAction()
		{
			
        $this->loadLayout();
			$this->getResponse()->setBody(
				$this->getLayout()->createBlock('adminhtml/catalog_product_grid')->toHtml()
				);
		}

	
		public function groupedAction()
		{
			$this->_initProduct();
			$gridBlock = $this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_related')
			->setGridUrl($this->getUrl('*/*/gridOnly', array('_current' => true, 'gridOnlyBlock' => 'related')))
			;
			$serializerBlock = $this->_createSerializerBlock('links[related]', $gridBlock, Mage::registry('product')->getRelatedProducts());

			$this->_outputBlocks($gridBlock, $serializerBlock);
		}
		
		public function gridAction()
		{
			$this->loadLayout();
			$this->getResponse()->setBody(
				$this->getLayout()->createBlock('adminhtml/catalog_product_grid')->toHtml()
				);
		}

		/**
		 * Get specified tab grid
		 */
		public function gridOnlyAction()
		{
			$this->_initProduct();
			$this->loadLayout();
			$this->getResponse()->setBody(
				$this->getLayout()->createBlock('adminhtml/catalog_product_edit_tab_' . $this->getRequest()->getParam('gridOnlyBlock'))
				->toHtml()
				);
		}
	 
}