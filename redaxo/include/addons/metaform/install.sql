CREATE TABLE `rex_62_section` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `submit_label` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

INSERT INTO `rex_62_section` VALUES (1, 'Onlinezeitraum', 'Zeitraum festlegen');
INSERT INTO `rex_62_section` VALUES (2, 'Sonstiges', 'Speichern');

CREATE TABLE `rex_62_field` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `section_id` int(10) unsigned NOT NULL default '0',
  `type_id` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `attribute` varchar(255) NOT NULL default '',
  `validate` varchar(255) NOT NULL default '',
  `validate_params` varchar(255) NOT NULL default '',
  `validate_message` varchar(255) NOT NULL default '',
  `extras` text NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

INSERT INTO `rex_62_field` VALUES (1, 1, 7, 'Von', '', '', '', '', '');
INSERT INTO `rex_62_field` VALUES (2, 1, 7, 'Bis', '', '', '', '', '');
INSERT INTO `rex_62_field` VALUES (3, 2, 5, 'Teaser', '', '', '', '', '');

CREATE TABLE `rex_62_type` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

INSERT INTO `rex_62_type` VALUES (1, 'text');
INSERT INTO `rex_62_type` VALUES (2, 'textarea');
INSERT INTO `rex_62_type` VALUES (3, 'select');
INSERT INTO `rex_62_type` VALUES (4, 'radio');
INSERT INTO `rex_62_type` VALUES (5, 'checkbox');
INSERT INTO `rex_62_type` VALUES (6, 'password');
INSERT INTO `rex_62_type` VALUES (7, 'dateselect');
INSERT INTO `rex_62_type` VALUES (8, 'REX_MEDIA_BUTTON');
INSERT INTO `rex_62_type` VALUES (9, 'REX_MEDIALIST_BUTTON');
INSERT INTO `rex_62_type` VALUES (10, 'REX_LINK_BUTTON');
INSERT INTO `rex_62_type` VALUES (11, 'REX_LINKLIST_BUTTON');

CREATE TABLE `rex_62_value` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `field_id` int(11) NOT NULL default '0',
  `article_id` int(11) NOT NULL default '0',
  `clang` int(11) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `unique_value` (`article_id`,`clang`,`field_id`)
) TYPE=MyISAM;