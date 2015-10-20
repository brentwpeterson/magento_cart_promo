<?php

class Wdc_Cartex_Block_Adminhtml_Entity_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

	protected $_cartexId;
	protected $_promotype;
	protected $_exceptiontype;
	
	public function __construct()
	{
		parent::__construct();
		$this->setId('entity_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle(Mage::helper('cartex')->__('Manage Cart Promos'));
		$this->_cartexId = Mage::app()->getFrontController()->getRequest()->get('id');	
		
	}

	protected function _beforeToHtml()
	{		
		$this->setTypeCodes();
		
		//echo $this->_promotype;
		
		$this->addTab('form_section', array(
			'label'     => Mage::helper('cartex')->__('Cart Promo Information'),
			'title'     => Mage::helper('cartex')->__('Cart Promo Information'),
			'content'   => $this->getLayout()->createBlock('cartex/adminhtml_entity_edit_tab_form')->toHtml(),
			));
		
		if($this->_promotype != null){	
			if($this->_promotype == 0 || $this->_promotype == 2 || $this->_promotype == 6 || $this->_promotype == 7){
				$this->addTab('value_section', array(
					'label'     => Mage::helper('cartex')->__('Value Based Rules '),
					'title'     => Mage::helper('cartex')->__('Value Based Rules'),
					'content'   => $this->getLayout()->createBlock('cartex/adminhtml_entity_edit_tab_pricerules')->toHtml(),
					));
			}
			
			if($this->_promotype == 12){
				$this->addTab('qty_section', array(
					'label'     => Mage::helper('cartex')->__('BuyX get something free'),
					'title'     => Mage::helper('cartex')->__('BuyX get something free'),
					'content'   => $this->getLayout()->createBlock('cartex/adminhtml_entity_edit_tab_buyxfree')->toHtml(),
					));
			}
			
//			if($this->_promotype == 5){
//				$this->addTab('buyx_section', array(
//					'label'     => Mage::helper('cartex')->__('BuyX get Cheapest Free'),
//					'title'     => Mage::helper('cartex')->__('BuyX get Cheapest Free'),
//					'content'   => $this->getLayout()->createBlock('cartex/adminhtml_entity_edit_tab_buyx')->toHtml(),
//					));
//			}
			
			if($this->_promotype == 2 || $this->_promotype == 3 || $this->_promotype == 4
				|| $this->_promotype == 7 || $this->_promotype == 8 || $this->_promotype == 9){
//				$this->addTab('coupon_section', array(
//					'label'     => Mage::helper('cartex')->__('Coupon Section'),
//					'title'     => Mage::helper('cartex')->__('Coupon Section'),
//					'content'   => $this->getLayout()->createBlock('cartex/adminhtml_entity_edit_tab_coupon')->toHtml(),
//					));
					
					$this->addTab('coupon_gen', array(
							'label'     => Mage::helper('cartex')->__('Coupon Generator'),
							'title'     => Mage::helper('cartex')->__('Coupon Generator'),
							'content'   => $this->getLayout()->createBlock('cartex/adminhtml_entity_edit_tab_coupgen')->toHtml(),
							));
					
					$this->addTab('coupon_grid', array(
							'label'     => Mage::helper('cartex')->__('Coupon Collection'),						
							'url'       => $this->getUrl('*/*/coupons', array('_current' => true)),
							'class'     => 'ajax',
							));
					
					$this->addTab('coupon_list', array(
							'label'     => Mage::helper('cartex')->__('Coupon list'),
							'title'     => Mage::helper('cartex')->__('Coupon list'),
							'content'   => $this->getLayout()->createBlock('cartex/adminhtml_entity_edit_tab_couponlist')->toHtml(),
							));
			}			
			
				if($this->_promotype == 1 || $this->_promotype == 3 || $this->_promotype == 6
					|| $this->_promotype == 7 || $this->_promotype == 9 || $this->_promotype == 11 || $this->_promotype == 12){
				if($this->_exceptiontype == 0){				
					$this->addTab('related', array(
						'label'     => Mage::helper('cartex')->__('Assigned Products'),						
						'url'       => $this->getUrl('*/*/related', array('_current' => true)),
						'class'     => 'ajax',
						));
				}
				elseif($this->_exceptiontype == 1){
					$this->addTab('categories', array(
						'label'     => Mage::helper('cartex')->__('Assigned Categories'),						
						'url'       => $this->getUrl('*/*/categories', array('_current' => true)),						
						'class'     => 'ajax',
						));
				}
				elseif($this->_exceptiontype == 2){
					$this->addTab('atributeset', array(
						'label'     => Mage::helper('cartex')->__('Assigned Attribute Sets'),						
						'url'       => $this->getUrl('*/*/attributes', array('_current' => true)),
						//'content'   => $this->getLayout()->createBlock('cartex/adminhtml_entity_edit_tab_attributesets')->toHtml(),											
						'class'     => 'ajax',
						));
				}
			}
			
			if($this->_promotype != 4) {
				$this->addTab('added', array(
					'label'     => Mage::helper('cartex')->__('Product to Add'),					
					'url'       => $this->getUrl('*/*/added', array('_current' => true)),
					'class'     => 'ajax',
					));
			}
				
			if($this->_promotype == 4 || $this->_promotype == 5) {
				$this->addTab('products', array(
					'label'     => Mage::helper('cartex')->__('Assign Grouped Products'),
					'url'       => $this->getUrl('*/*/products', array('_current' => true)),
					'class'     => 'ajax',
					));
			}
		}
		

		
		return parent::_beforeToHtml();
	}
	
	protected function setTypeCodes()
	{
		$this->_promotype = $this->getPromoType();
		$this->_exceptiontype = $this->getExceptionType(); 	
	}
	
	protected function getPromoType()
	{
		$this->_promotype =  Mage::getModel('cartex/cart_entity')->load($this->_cartexId)->getPromoType();
		return $this->_promotype;
	}
	
	protected function getExceptionType()
	{
		$this->_exceptiontype =  Mage::getModel('cartex/cart_entity')->load($this->_cartexId)->getExceptionTypeId();
		return $this->_exceptiontype;
	}
	
	
	protected function _toHtml()
	{
		$url = Mage::getModel('adminhtml/url')->getUrl('cartex/adminhtml_coupon/create');
		
		$sContent = parent::_toHtml();
		
			$sContent .= '
       
	
	<script type="text/javascript">
	var xmlhttp;
	
	function GetXmlHttpObject() {
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        return new XMLHttpRequest();
    }
    if (window.ActiveXObject) {
        // code for IE6, IE5
        return new ActiveXObject("Microsoft.XMLHTTP");
    }
    return null;
	}
	
	function sendcodes(){
	 
	var pre = document.getElementById("code_prefix").value;
	var num = document.getElementById("coupon_num").value;
	var len = document.getElementById("coupon_len").value;
	var dis = document.getElementById("coupon_discount").value;
	var use = document.getElementById("coupon_use").value;
	var custuse = document.getElementById("cust_use").value;
	var id = document.getElementById("cartex_id").value;
	
	xmlhttp = GetXmlHttpObject();
    if (xmlhttp == null) {
        alert("Browser does not support HTTP Request");
        return;
    }
	var url = "'.$url.'";
    url = url + "code/" + pre + "/coupon_num/" + num + "/coupon_len/" + len + "/id/" + id + "/use/" + use + "/discount/" + dis + "/cust_use/" + custuse + "/";

    xmlhttp.onreadystatechange = stateChanged;
    xmlhttp.open("GET", url, true);
    xmlhttp.send(null);
	
	}
	
function stateChanged() {
   if (xmlhttp.readyState == 4) {
       document.getElementById("coupon_text").innerHTML = xmlhttp.responseText;
	  //alert(xmlhttp.responseText);
  }
}
	</script>
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