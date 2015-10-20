<?php
class Wdc_Cartex_Model_Mysql4_Template extends Mage_Core_Model_Mysql4_Abstract
{
	protected function _construct()
	{
		$this->_init('cartex/template', 'template_id');
	}		
	

}