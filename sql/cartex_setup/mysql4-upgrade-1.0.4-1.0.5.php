<?php

$installer = $this;

$installer->startSetup();

$installer->run("	

ALTER TABLE {$this->getTable('wdc_cartex_exception_entity')}
	ADD COLUMN `one_one` TINYINT(1) NOT NULL DEFAULT 0  AFTER `item_limit` ;
	
ALTER TABLE {$this->getTable('wdc_cartex_exception_groups')}
  ADD CONSTRAINT `FK_CARTEX_ID_ENTITY`
  FOREIGN KEY (`wdc_attribute_id` )
  REFERENCES {$this->getTable('wdc_cartex_exception_entity')} (`cartex_id` )
  ON DELETE CASCADE
  ON UPDATE CASCADE
, ADD INDEX `FK_CARTEX_ID_ENTITY` (`wdc_attribute_id` ASC) ;


ALTER TABLE {$this->getTable('wdc_cartex_coupon')}
DROP PRIMARY KEY 
, ADD PRIMARY KEY (`coupon_id`) ;

ALTER TABLE {$this->getTable('wdc_cartex_exception_item_entity')}
  ADD CONSTRAINT `FK_CARTEX_ID_ITEM_ENTITY`
  FOREIGN KEY (`wdc_attribute_id` )
  REFERENCES {$this->getTable('wdc_cartex_exception_entity')} (`cartex_id` )
  ON DELETE CASCADE
  ON UPDATE CASCADE
, ADD INDEX `FK_CARTEX_ID_ITEM_ENTITY` (`wdc_attribute_id` ASC) ;

ALTER TABLE {$this->getTable('wdc_cartex_exception_value')} 
DROP PRIMARY KEY 
, ADD PRIMARY KEY (`value_id`) ;

ALTER TABLE {$this->getTable('wdc_cartex_exception_value')} 
  ADD CONSTRAINT `PK_CARTEX_ID_VALUE_ENTITY`
  FOREIGN KEY (`wdc_attribute_id` )
  REFERENCES {$this->getTable('wdc_cartex_exception_entity')} (`cartex_id` )
  ON DELETE CASCADE
  ON UPDATE CASCADE
, ADD INDEX `PK_CARTEX_ID_VALUE_ENTITY` (`wdc_attribute_id` ASC) ;

ALTER TABLE {$this->getTable('wdc_cartex_item_idx')}
DROP PRIMARY KEY 
, ADD PRIMARY KEY (`item_idx_id`, `product_id`, `quote_id`) ;

");

$installer->endSetup(); 


