<?php

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('wdc_cartex_exception_entity')} 
ADD COLUMN `exception_type_id` INT(10) NOT NULL DEFAULT 0  AFTER `entity_type_id` ;

ALTER TABLE {$this->getTable('wdc_cartex_exception_groups')}  
DROP INDEX `IDX_ATTRIBUTE_VALUE` 
, ADD UNIQUE INDEX `IDX_ATTRIBUTE_VALUE` (`wdc_attribute_id` ASC, `entity_id` ASC, `entity_type_id` ASC, `attribute_set_id` ASC) ;

");

$installer->endSetup(); 


