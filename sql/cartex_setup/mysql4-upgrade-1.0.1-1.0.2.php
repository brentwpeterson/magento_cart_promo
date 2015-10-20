<?php

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('wdc_cartex_exception_entity')} 
ADD COLUMN `discount_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00  AFTER `rule_id` , 
ADD COLUMN `entity_id` INT(10) NOT NULL DEFAULT 0  AFTER `discount_amount` , 
ADD COLUMN `entity_type_id` INT(10) NOT NULL DEFAULT 0  AFTER `entity_id` ;
");

$installer->endSetup(); 


