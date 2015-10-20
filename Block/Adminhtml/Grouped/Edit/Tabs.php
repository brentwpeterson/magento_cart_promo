<?php

class Wdc_Cartex_Block_Adminhtml_Grouped_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
 
	public function __construct()
	{
		parent::__construct();
		$this->setId('faq_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('faq')->__('FAQ Info'));
		
	
	}
	
	protected function _beforeToHtml()
	{
		$this->addTab('form_section', array(
			'label'     => Mage::helper('faq')->__('Item Information'),
			'title'     => Mage::helper('faq')->__('Item Information'),
			'content'   => $this->getLayout()->createBlock('faq/adminhtml_faq_edit_tab_form')->toHtml(),
			));
		
		$this->addTab('long_section', array(
			'label'     => Mage::helper('faq')->__('Long Description'),
			'title'     => Mage::helper('faq')->__('Long Description'),
			'content'   => $this->getLayout()->createBlock('faq/adminhtml_faq_edit_tab_main')->toHtml(),
			));
			
		$this->addTab('meta_section', array(
			'label'     => Mage::helper('faq')->__('Meta Information'),
			'title'     => Mage::helper('faq')->__('Meta Information'),
			'content'   => $this->getLayout()->createBlock('faq/adminhtml_faq_edit_tab_meta')->toHtml(),
			));
		$this->addTab('category_section', array(
			'label'     => Mage::helper('faq')->__('Categories'),
			'title'     => Mage::helper('faq')->__('Categories'),
			'content'   => $this->getLayout()->createBlock('faq/adminhtml_faq_edit_tab_categories')->toHtml(),
			));
		$this->addTab('related', array(
			'label'     => Mage::helper('catalog')->__('Assigned Products'),
			//'content'   => $this->getLayout()->createBlock('faq/adminhtml_faq_edit_tab_related')->toHtml(),
			'url'       => $this->getUrl('*/*/related', array('_current' => true)),
			'class'     => 'ajax',
			));
		
		return parent::_beforeToHtml();
	}
	
	protected function _toHtml()
	{
		$sContent = parent::_toHtml();
		
		$sContent .= '
        
<script type="text/javascript">
//<![CDATA[        
        
    var productLinksController = Class.create();

    productLinksController.prototype = {
        initialize : function(fieldId, products, grid) {
            this.saveField = $(fieldId);
            this.saveFieldId = fieldId;
            this.products    = $H(products);
            this.grid        = grid;
            this.tabIndex    = 1000;
            this.grid.rowClickCallback = this.rowClick.bind(this);
            this.grid.initRowCallback = this.rowInit.bind(this);
            this.grid.checkboxCheckCallback = this.registerProduct.bind(this);
            this.grid.rows.each(this.eachRow.bind(this));
            this.saveField.value = this.serializeObject(this.products);
            this.grid.reloadParams = {"products[]":this.products.keys()};
        },
        eachRow : function(row) {
            this.rowInit(this.grid, row);
        },
        registerProduct : function(grid, element, checked) {
            if(checked){
                if(element.inputElements) {
                    this.products.set(element.value, {});
                    for(var i = 0; i < element.inputElements.length; i++) {
                        element.inputElements[i].disabled = false;
                        this.products.get(element.value)[element.inputElements[i].name] = element.inputElements[i].value;
                    }
                }
            }
            else{
                if(element.inputElements){
                    for(var i = 0; i < element.inputElements.length; i++) {
                        element.inputElements[i].disabled = true;
                    }
                }

                this.products.unset(element.value);
            }
            this.saveField.value = this.serializeObject(this.products);
            this.grid.reloadParams = {"products[]":this.products.keys()};
        },
        serializeObject : function(hash) {
            var clone = hash.clone();
            clone.each(function(pair) {
                clone.set(pair.key, encode_base64(Object.toQueryString(pair.value)));
            });
            return clone.toQueryString();
        },
        rowClick : function(grid, event) {
            var trElement = Event.findElement(event, "tr");
            var isInput   = Event.element(event).tagName == "INPUT";
            if(trElement){
                var checkbox = Element.select(trElement, "input");
                if(checkbox[0]){
                    var checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                    this.grid.setCheckboxChecked(checkbox[0], checked);
                }
            }
        },
        inputChange : function(event) {
            var element = Event.element(event);
            if(element && element.checkboxElement && element.checkboxElement.checked){
                this.products.get(element.checkboxElement.value)[element.name] = element.value;
                this.saveField.value = this.serializeObject(this.products);
            }
        },
        rowInit : function(grid, row) {
            var checkbox = $(row).select(".checkbox")[0];
            var inputs = $(row).select(".input-text");
            if(checkbox && inputs.length > 0) {
                checkbox.inputElements = inputs;
                for(var i = 0; i < inputs.length; i++) {
                    inputs[i].checkboxElement = checkbox;
                    if(this.products.get(checkbox.value) && this.products.get(checkbox.value)[inputs[i].name]) {
                        inputs[i].value = this.products.get(checkbox.value)[inputs[i].name];
                    }
                    inputs[i].disabled = !checkbox.checked;
                    inputs[i].tabIndex = this.tabIndex++;
                    Event.observe(inputs[i],"keyup", this.inputChange.bind(this));
                    Event.observe(inputs[i],"change", this.inputChange.bind(this));
                }
            }
        }
    };        
//]]>
</script>        
        ';
		
		return $sContent;
	}
}