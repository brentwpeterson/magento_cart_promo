<?php

$installer = $this;

$installer->startSetup();

$installer->run("	

ALTER TABLE {$this->getTable('wdc_cartex_item_idx')}

  ADD CONSTRAINT `FK_ITEM_ID_IDEX`
  FOREIGN KEY (`item_id` )
  REFERENCES {$this->getTable('sales_flat_quote_item')} (`item_id` )
  ON DELETE CASCADE
  ON UPDATE CASCADE, 
  ADD INDEX `FK_ITEM_ID_IDEX` (`item_id` ASC) ;
  
  ALTER TABLE {$this->getTable('wdc_cartex_coupon')}
  CHANGE COLUMN `cartex_id` `cartex_id` 
  INT(10) UNSIGNED NOT NULL DEFAULT '0', 
  ADD INDEX `IDX_CARTEX_ID` (`cartex_id` ASC) ;
  
  ALTER TABLE {$this->getTable('wdc_cartex_coupon')} 
  ADD CONSTRAINT `FK_CARTEXID_CPN`
  FOREIGN KEY (`cartex_id` )
  REFERENCES {$this->getTable('wdc_cartex_exception_entity')} (`cartex_id` )
  ON DELETE CASCADE
  ON UPDATE CASCADE
, ADD INDEX `FK_CARTEXID_CPN` (`cartex_id` ASC) ;

ALTER TABLE {$this->getTable('wdc_cartex_item_idx')} 
CHANGE COLUMN `cartex_id` `cartex_id` INT(10) UNSIGNED NOT NULL DEFAULT '0'  ;

ALTER TABLE {$this->getTable('wdc_cartex_item_idx')} 
  ADD CONSTRAINT `FK_CARTEXID_IDX`
  FOREIGN KEY (`cartex_id` )
  REFERENCES {$this->getTable('wdc_cartex_exception_entity')} (`cartex_id` )
  ON DELETE NO ACTION
  ON UPDATE NO ACTION
, ADD INDEX `FK_CARTEXID_IDX` (`cartex_id` ASC) ;



");

$installer->endSetup(); 


