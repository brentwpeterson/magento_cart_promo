<?php

$installer = $this;

$installer->startSetup();

$installer->run("

CREATE TABLE {$this->getTable('wdc_cartex_exception_entity')} (
  `cartex_id` int(10) unsigned NOT NULL auto_increment,
  `promo_name` varchar(255) NOT NULL default '',
  `promo_code` varchar(45) NOT NULL default '',
  `description` text NOT NULL,
  `promo_type` int(10) unsigned NOT NULL default '0',
  `from_date` date default '0000-00-00',
  `to_date` date default '0000-00-00',
  `rule_id` int(11) NOT NULL default '0',
  `is_active` tinyint(1) NOT NULL default '0',
  `sort_order` int(10) unsigned NOT NULL default '0',
  `store_id` int(10) unsigned NOT NULL default '0',
  `use_rules` tinyint(1) unsigned NOT NULL default '0',
  `has_options` tinyint(1) NOT NULL default '0',
  `item_limit` int(10) NOT NULL default '1',
  PRIMARY KEY  (`cartex_id`),
  KEY `sort_order` (`is_active`,`sort_order`,`to_date`,`from_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('wdc_cartex_exception_groups')} (
   `wdc_id` int(10) unsigned NOT NULL auto_increment,
  `attribute_set_id` int(10) unsigned NOT NULL default '0',
  `entity_type_id` int(10) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `wdc_attribute_id` int(10) unsigned NOT NULL default '1',
  `wdc_override` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`wdc_id`),
  UNIQUE KEY `IDX_ATTRIBUTE_VALUE` (`wdc_attribute_id`,`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE {$this->getTable('wdc_cartex_exception_item_entity')} (
  `wdc_id` int(10) unsigned NOT NULL auto_increment,
  `entity_id` int(10) unsigned NOT NULL default '0',
  `wdc_attribute_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(45) default NULL,
  `sku` varchar(45) default NULL,
  `ex_limit` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`wdc_id`),
  UNIQUE KEY `IDX_ATTRIBUTE_VALUE` (`entity_id`,`wdc_attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE {$this->getTable('wdc_cartex_exception_value')} (
  `value_id` int(10) unsigned NOT NULL auto_increment,
  `wdc_attribute_id` int(10) unsigned NOT NULL default '0',
  `wdc_exception_group_id` int(10) unsigned NOT NULL default '0',
  `wdc_exception_item_id` int(10) unsigned NOT NULL default '0',
  `store_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(45) collate utf8_unicode_ci NOT NULL default '0',
  `sort_order` int(10) unsigned NOT NULL default '0',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `min_val` int(10) NOT NULL default '0',
  `max_val` int(10) NOT NULL default '0',
  `qual_statement` varchar(45) collate utf8_unicode_ci NOT NULL default '=',
  PRIMARY KEY  (`value_id`,`wdc_attribute_id`),
  UNIQUE KEY `wdc_attribute_id_UNIQUE` (`wdc_attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE {$this->getTable('wdc_cartex_promo_product')} (
  `promo_product_id` int(10) unsigned NOT NULL auto_increment,
  `entity_id` int(10) unsigned NOT NULL default '0',
  `wdc_attribute_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`promo_product_id`,`entity_id`),
  UNIQUE KEY `UNQ_ATTRIBUTE_ID` (`entity_id`,`wdc_attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup(); 


