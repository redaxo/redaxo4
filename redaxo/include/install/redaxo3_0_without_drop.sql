-- phpMyAdmin SQL Dump
-- version 2.6.0-pl3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Erstellungszeit: 26. April 2005 um 16:28
-- Server Version: 3.23.58
-- PHP-Version: 4.3.10
-- 
-- Datenbank: `redaxo3_0`
-- 

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `rex_5_article_comment`
-- 

CREATE TABLE `rex_5_article_comment` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default '0',
  `article_id` int(11) default '0',
  `comment` text,
  `stamp` int(11) default '0',
  `status` tinyint(4) default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `rex_5_article_comment`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `rex_5_board`
-- 

CREATE TABLE `rex_5_board` (
  `message_id` int(11) NOT NULL auto_increment,
  `re_message_id` int(11) default '0',
  `board_id` varchar(255) default NULL,
  `user_id` int(11) default '0',
  `replies` int(11) default '0',
  `last_entry` varchar(14) default NULL,
  `subject` varchar(255) default NULL,
  `message` text,
  `stamp` varchar(14) default NULL,
  `status` int(11) default '1',
  `anonymous_user` varchar(50) default NULL,
  PRIMARY KEY  (`message_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `rex_5_board`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `rex_5_session`
-- 

CREATE TABLE `rex_5_session` (
  `session` varchar(50) NOT NULL default '',
  `user_id` int(11) default '0',
  `name` varchar(50) default NULL,
  `stamp` int(11) default '0',
  PRIMARY KEY  (`session`)
) TYPE=MyISAM;

-- 
-- Daten für Tabelle `rex_5_session`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `rex_5_user`
-- 

CREATE TABLE `rex_5_user` (
  `id` int(11) NOT NULL auto_increment,
  `login` varchar(255) default NULL,
  `psw` varchar(255) default NULL,
  `email` varchar(255) default NULL,
  `name` varchar(255) default NULL,
  `firstname` varchar(255) default NULL,
  `sex` char(1) default NULL,
  `singlestatus` varchar(25) default '0',
  `street` varchar(255) default NULL,
  `zip` varchar(5) default NULL,
  `city` varchar(255) default NULL,
  `phone` varchar(255) default NULL,
  `fax` varchar(255) default NULL,
  `mobil` varchar(255) default NULL,
  `posx` int(11) default '0',
  `posy` int(11) default '0',
  `file` varchar(255) default NULL,
  `birthday` varchar(14) default '0',
  `interests` text,
  `motto` text,
  `ilike` text,
  `aboutme` text,
  `size` varchar(255) default NULL,
  `wheight` varchar(255) default NULL,
  `color_eyes` varchar(255) default NULL,
  `color_hair` varchar(255) default NULL,
  `profession` varchar(255) default NULL,
  `homepage` varchar(255) default NULL,
  `newsletter` tinyint(1) default '0',
  `check1` tinyint(1) default '0',
  `check2` tinyint(1) default '0',
  `check3` tinyint(1) default '0',
  `check4` tinyint(1) default '0',
  `amount_profile_viewed` int(11) default '0',
  `amount_comments` int(11) default '0',
  `amount_board_topics` int(11) default '0',
  `amount_board_replies` int(11) default '0',
  `amount_mail_inbox` int(11) default '0',
  `amount_mail_outbox` int(11) default '0',
  `showinfo` tinyint(1) default '0',
  `first_login` int(11) default '0',
  `last_login` int(11) default '0',
  `last_action` int(11) default '0',
  `stamp` int(11) default '0',
  `status` tinyint(4) default '1',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `rex_5_user`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `rex_5_user_comment`
-- 

CREATE TABLE `rex_5_user_comment` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default '0',
  `from_user_id` int(11) default '0',
  `message` text,
  `stamp` varchar(14) default NULL,
  `status` tinyint(1) default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `rex_5_user_comment`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `rex_5_user_mail`
-- 

CREATE TABLE `rex_5_user_mail` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default '0',
  `from_user_id` int(11) default '0',
  `message` text,
  `stamp` varchar(14) default NULL,
  `status` tinyint(1) default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `rex_5_user_mail`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `rex_action`
-- 

CREATE TABLE `rex_action` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `action` text,
  `prepost` tinyint(4) default '0',
  `status` tinyint(4) default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `rex_action`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `rex_article`
-- 

CREATE TABLE `rex_article` (
  `id` int(11) default '0',
  `re_id` int(11) default '0',
  `name` varchar(255) NOT NULL default '',
  `catname` varchar(255) NOT NULL default '',
  `cattype` int(11) default '0',
  `alias` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `attribute` text NOT NULL,
  `file` text NOT NULL,
  `type_id` int(11) default '0',
  `teaser` tinyint(4) default '0',
  `startpage` tinyint(1) default '0',
  `prior` int(11) default '0',
  `path` varchar(255) NOT NULL default '',
  `status` tinyint(1) default '0',
  `online_from` int(11) default '0',
  `online_to` int(11) default '0',
  `createdate` int(11) default '0',
  `updatedate` int(11) NOT NULL default '0',
  `keywords` text NOT NULL,
  `template_id` int(5) default '0',
  `clang` int(11) default '0',
  `createuser` varchar(255) NOT NULL default '',
  `updateuser` varchar(255) NOT NULL default ''
) TYPE=MyISAM;

-- 
-- Daten für Tabelle `rex_article`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `rex_article_slice`
-- 

CREATE TABLE `rex_article_slice` (
  `id` int(11) NOT NULL auto_increment,
  `clang` int(11) NOT NULL default '0',
  `ctype` int(11) NOT NULL default '0',
  `re_article_slice_id` int(11) NOT NULL default '0',
  `value1` text,
  `value2` text,
  `value3` text,
  `value4` text,
  `value5` text,
  `value6` text,
  `value7` text,
  `value8` text,
  `value9` text,
  `value10` text,
  `value11` text NOT NULL,
  `value12` text NOT NULL,
  `value13` text NOT NULL,
  `value14` text NOT NULL,
  `value15` text NOT NULL,
  `value16` text NOT NULL,
  `value17` text NOT NULL,
  `value18` text NOT NULL,
  `value19` text NOT NULL,
  `value20` text NOT NULL,
  `file1` varchar(255) default NULL,
  `file2` varchar(255) default NULL,
  `file3` varchar(255) default NULL,
  `file4` varchar(255) default NULL,
  `file5` varchar(255) default NULL,
  `file6` varchar(255) default NULL,
  `file7` varchar(255) default NULL,
  `file8` varchar(255) default NULL,
  `file9` varchar(255) default NULL,
  `file10` varchar(255) default NULL,
  `filelist1` text NOT NULL,
  `filelist2` text NOT NULL,
  `filelist3` text NOT NULL,
  `filelist4` text NOT NULL,
  `filelist5` text NOT NULL,
  `filelist6` text NOT NULL,
  `filelist7` text NOT NULL,
  `filelist8` text NOT NULL,
  `filelist9` text NOT NULL,
  `filelist10` text NOT NULL,
  `link1` int(11) default '0',
  `link2` int(11) default '0',
  `link3` int(11) default '0',
  `link4` int(11) default '0',
  `link5` int(11) default '0',
  `link6` int(11) default '0',
  `link7` int(11) default '0',
  `link8` int(11) default '0',
  `link9` int(11) default '0',
  `link10` int(11) default '0',
  `linklist1` text NOT NULL,
  `linklist2` text NOT NULL,
  `linklist3` text NOT NULL,
  `linklist4` text NOT NULL,
  `linklist5` text NOT NULL,
  `linklist6` text NOT NULL,
  `linklist7` text NOT NULL,
  `linklist8` text NOT NULL,
  `linklist9` text NOT NULL,
  `linklist10` text NOT NULL,
  `php` text,
  `html` text,
  `article_id` int(11) NOT NULL default '0',
  `modultyp_id` int(5) NOT NULL default '0',
  `createdate` int(11) NOT NULL default '0',
  `updatedate` int(11) NOT NULL default '0',
  `createuser` varchar(255) NOT NULL default '',
  `updateuser` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`,`re_article_slice_id`,`article_id`,`modultyp_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `rex_article_slice`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `rex_article_type`
-- 

CREATE TABLE `rex_article_type` (
  `type_id` int(5) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `description` text,
  PRIMARY KEY  (`type_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `rex_article_type`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `rex_clang`
-- 

CREATE TABLE `rex_clang` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Daten für Tabelle `rex_clang`
-- 

INSERT INTO `rex_clang` (`id`, `name`) VALUES (0, 'deutsch');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `rex_file`
-- 

CREATE TABLE `rex_file` (
  `file_id` int(11) NOT NULL auto_increment,
  `ctype` int(11) default '0',
  `re_file_id` int(11) default '0',
  `category_id` int(11) default '0',
  `filetype` varchar(255) default NULL,
  `filename` varchar(255) default NULL,
  `originalname` varchar(255) default NULL,
  `filesize` varchar(255) default NULL,
  `width` int(11) default '0',
  `height` int(11) default '0',
  `title` varchar(255) default NULL,
  `description` text,
  `copyright` varchar(255) default NULL,
  `stamp` int(11) default '0',
  `user_login` varchar(255) default NULL,
  `createdate` int(11) NOT NULL default '0',
  `updatedate` int(11) NOT NULL default '0',
  `createuser` varchar(255) NOT NULL default '',
  `updateuser` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`file_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `rex_file`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `rex_file_category`
-- 

CREATE TABLE `rex_file_category` (
  `id` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `re_id` int(11) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  `clang` int(11) NOT NULL default '0',
  `hide` tinyint(4) NOT NULL default '0',
  `createdate` int(11) NOT NULL default '0',
  `updatedate` int(11) NOT NULL default '0',
  `createuser` varchar(255) NOT NULL default '',
  `updateuser` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Daten für Tabelle `rex_file_category`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `rex_help`
-- 

CREATE TABLE `rex_help` (
  `id` int(11) NOT NULL auto_increment,
  `page` varchar(255) default NULL,
  `name` varchar(255) default NULL,
  `description` text,
  `comment` text,
  `lang` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `rex_help`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `rex_module_action`
-- 

CREATE TABLE `rex_module_action` (
  `id` int(11) NOT NULL auto_increment,
  `module_id` int(11) default '0',
  `action_id` int(11) default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `rex_module_action`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `rex_modultyp`
-- 

CREATE TABLE `rex_modultyp` (
  `id` int(5) NOT NULL auto_increment,
  `label` varchar(255) default NULL,
  `name` varchar(255) default NULL,
  `category_id` int(11) NOT NULL default '0',
  `ausgabe` text,
  `bausgabe` text,
  `eingabe` text,
  `func` text,
  `php_enable` tinyint(1) NOT NULL default '0',
  `html_enable` tinyint(1) NOT NULL default '0',
  `perm_category` text NOT NULL,
  PRIMARY KEY  (`id`,`category_id`,`php_enable`,`html_enable`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `rex_modultyp`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `rex_template`
-- 

CREATE TABLE `rex_template` (
  `id` int(5) NOT NULL auto_increment,
  `label` varchar(255) default NULL,
  `name` varchar(255) default NULL,
  `content` text,
  `bcontent` text,
  `active` tinyint(1) default '0',
  `date` timestamp(14) NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `rex_template`
-- 


-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `rex_user`
-- 

CREATE TABLE `rex_user` (
  `user_id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `login` varchar(50) NOT NULL default '',
  `psw` varchar(50) NOT NULL default '',
  `status` varchar(5) NOT NULL default '',
  `rights` text NOT NULL,
  `login_tries` tinyint(4) NOT NULL default '0',
  `createdate` int(11) NOT NULL default '0',
  `updatedate` int(11) NOT NULL default '0',
  `lasttrydate` int(11) NOT NULL default '0',
  `session_id` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`user_id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Daten für Tabelle `rex_user`
-- 

