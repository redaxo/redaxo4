DROP TABLE IF EXISTS `rex_11_excel_export`;
CREATE TABLE IF NOT EXISTS `rex_11_excel_export` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tbl_name` varchar(255) NOT NULL default '',
  `rows` int(11) NOT NULL default '0',
  `exportuser` varchar(255) NOT NULL default '',
  `exportdate` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `rex_11_excel_export_tbl`;
CREATE TABLE IF NOT EXISTS `rex_11_excel_export_tbl` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tbl_name` varchar(255) NOT NULL default '',
  `tbl_label` varchar(255) NOT NULL default '',
  `tbl_pk` varchar(255) NOT NULL default '',
  `updateuser` varchar(255) NOT NULL default '',
  `updatedate` int(11) NOT NULL default '0',
  `createuser` varchar(255) NOT NULL default '',
  `createdate` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;