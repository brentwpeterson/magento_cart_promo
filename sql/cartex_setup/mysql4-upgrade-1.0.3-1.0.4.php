<?php

$installer = $this;

$installer->startSetup();

$installer->run("
	
CREATE TABLE {$this->getTable('wdc_cartex_item_idx')} (
  `item_idx_id` int(10) unsigned NOT NULL auto_increment,
  `item_id` int(10) unsigned NOT NULL default '0',
  `product_id` int(10) NOT NULL default '0',
  `qty` int(10) NOT NULL default '0',
  `quote_id` int(10) NOT NULL default '0',
  `cartex_id` int(10) NOT NULL default '0',
  `is_discount` tinyint(1) NOT NULL default '0',
  `is_current` tinyint(1) NOT NULL default '1',
  `wdc_attribute_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`item_idx_id`,`item_id`,`product_id`,`quote_id`,`cartex_id`),
  UNIQUE KEY `UNQ_ATTRIBUTE_ID` (`item_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('wdc_cartex_coupon')} (
  `coupon_id` int(10) unsigned NOT NULL auto_increment,
  `coupon_code` varchar(45) NOT NULL default '',
  `discount_amount` decimal(12,2) NOT NULL default '0.00',
  `rule_id` int(10) unsigned NOT NULL default '0',
  `cartex_id` int(10) NOT NULL default '0',
  `use_rules` tinyint(1) NOT NULL default '0',
  `is_current` tinyint(1) NOT NULL default '1',
  `wdc_attribute_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`coupon_id`,`rule_id`,`cartex_id`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup(); 

