<?php

$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('wdc_cartex_exception_entity')}
 ADD COLUMN `stop_rules` TINYINT(1) NOT NULL DEFAULT 0  AFTER `one_one` ;
");
$installer->endSetup(); 


