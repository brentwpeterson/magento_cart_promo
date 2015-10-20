<?php

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('wdc_cartex_promo_product')} ADD COLUMN `parent_id` INT(10) NOT NULL DEFAULT 0  AFTER `wdc_attribute_id` ;
");

$installer->endSetup(); 


