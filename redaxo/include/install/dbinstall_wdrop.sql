# phpMyAdmin MySQL-Dump
# version 2.3.0-rc2
# http://phpwizard.net/phpMyAdmin/
# http://www.phpmyadmin.net/ (download page)
#
# Host: localhost
# Generation Time: Feb 05, 2004 at 02:26 PM
# Server version: 3.23.37
# PHP Version: 4.2.3
# Database : `redaxo2_6_standard`
# --------------------------------------------------------

#
# Table structure for table `rex__article_comment`
#

DROP TABLE IF EXISTS rex__article_comment;
CREATE TABLE rex__article_comment (
  id int(11) NOT NULL auto_increment,
  user_id int(11) default '0',
  article_id int(11) default '0',
  comment text,
  stamp int(11) default '0',
  status tinyint(4) default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Dumping data for table `rex__article_comment`
#

# --------------------------------------------------------

#
# Table structure for table `rex__board`
#

DROP TABLE IF EXISTS rex__board;
CREATE TABLE rex__board (
  message_id int(11) NOT NULL auto_increment,
  re_message_id int(11) default '0',
  board_id varchar(255) default NULL,
  user_id int(11) default '0',
  replies int(11) default '0',
  last_entry varchar(14) default NULL,
  subject varchar(255) default NULL,
  message text,
  stamp varchar(14) default NULL,
  status int(11) default '1',
  anonymous_user varchar(50) default NULL,
  PRIMARY KEY  (message_id)
) TYPE=MyISAM;

#
# Dumping data for table `rex__board`
#

# --------------------------------------------------------

#
# Table structure for table `rex__user`
#

DROP TABLE IF EXISTS rex__user;
CREATE TABLE rex__user (
  id int(11) NOT NULL auto_increment,
  login varchar(255) default NULL,
  psw varchar(255) default NULL,
  email varchar(255) default NULL,
  name varchar(255) default NULL,
  firstname varchar(255) default NULL,
  sex char(1) default NULL,
  singlestatus varchar(25) default '0',
  street varchar(255) default NULL,
  zip varchar(5) default NULL,
  city varchar(255) default NULL,
  phone varchar(255) default NULL,
  fax varchar(255) default NULL,
  mobil varchar(255) default NULL,
  posx int(11) default '0',
  posy int(11) default '0',
  file varchar(255) default NULL,
  birthday varchar(14) default '0',
  interests text,
  motto text,
  ilike text,
  aboutme text,
  size varchar(255) default NULL,
  wheight varchar(255) default NULL,
  color_eyes varchar(255) default NULL,
  color_hair varchar(255) default NULL,
  profession varchar(255) default NULL,
  homepage varchar(255) default NULL,
  newsletter tinyint(1) default '0',
  check1 tinyint(1) default '0',
  check2 tinyint(1) default '0',
  check3 tinyint(1) default '0',
  check4 tinyint(1) default '0',
  amount_profile_viewed int(11) default '0',
  amount_comments int(11) default '0',
  amount_board_topics int(11) default '0',
  amount_board_replies int(11) default '0',
  amount_mail_inbox int(11) default '0',
  amount_mail_outbox int(11) default '0',
  showinfo tinyint(1) default '0',
  first_login int(11) default '0',
  last_login int(11) default '0',
  last_action int(11) default '0',
  stamp int(11) default '0',
  status tinyint(4) default '1',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Dumping data for table `rex__user`
#

# --------------------------------------------------------

#
# Table structure for table `rex__user_comment`
#

DROP TABLE IF EXISTS rex__user_comment;
CREATE TABLE rex__user_comment (
  id int(11) NOT NULL auto_increment,
  user_id int(11) default '0',
  from_user_id int(11) default '0',
  message text,
  stamp varchar(14) default NULL,
  status tinyint(1) default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Dumping data for table `rex__user_comment`
#

# --------------------------------------------------------

#
# Table structure for table `rex__user_mail`
#

DROP TABLE IF EXISTS rex__user_mail;
CREATE TABLE rex__user_mail (
  id int(11) NOT NULL auto_increment,
  user_id int(11) default '0',
  from_user_id int(11) default '0',
  message text,
  stamp varchar(14) default NULL,
  status tinyint(1) default '0',
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Dumping data for table `rex__user_mail`
#

# --------------------------------------------------------

#
# Table structure for table `rex_article`
#

DROP TABLE IF EXISTS rex_article;
CREATE TABLE rex_article (
  id int(11) NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  beschreibung text NOT NULL,
  attribute text NOT NULL,
  file text NOT NULL,
  category_id int(11) NOT NULL default '0',
  type_id int(11) NOT NULL default '1',
  startpage tinyint(1) NOT NULL default '0',
  prior int(10) NOT NULL default '',
  path varchar(255) NOT NULL default '',
  status tinyint(1) NOT NULL default '0',
  online_von varchar(8) NOT NULL default '0',
  online_bis varchar(8) NOT NULL default '0',
  erstelldatum varchar(8) NOT NULL default '0',
  suchbegriffe text NOT NULL,
  template_id int(5) NOT NULL default '0',
  checkbox01 tinyint(1) NOT NULL default '0',
  checkbox02 tinyint(1) NOT NULL default '0',
  checkbox03 tinyint(1) NOT NULL default '0',
  checkbox04 tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (id,category_id,startpage,template_id)
) TYPE=MyISAM;

#
# Dumping data for table `rex_article`
#

# --------------------------------------------------------

#
# Table structure for table `rex_article_slice`
#

DROP TABLE IF EXISTS rex_article_slice;
CREATE TABLE rex_article_slice (
  id int(11) NOT NULL auto_increment,
  re_article_slice_id int(11) NOT NULL default '0',
  value1 text NOT NULL,
  value2 text NOT NULL,
  value3 text NOT NULL,
  value4 text NOT NULL,
  value5 text NOT NULL,
  value6 text NOT NULL,
  value7 text NOT NULL,
  value8 text NOT NULL,
  value9 text NOT NULL,
  value10 text NOT NULL,
  file1 varchar(255) NOT NULL default '',
  file2 varchar(255) NOT NULL default '',
  file3 varchar(255) NOT NULL default '',
  file4 varchar(255) NOT NULL default '',
  file5 varchar(255) NOT NULL default '',
  file6 varchar(255) NOT NULL default '',
  file7 varchar(255) NOT NULL default '',
  file8 varchar(255) NOT NULL default '',
  file9 varchar(255) NOT NULL default '',
  file10 varchar(255) NOT NULL default '',
  link1 int(11) NOT NULL default '0',
  link2 int(11) NOT NULL default '0',
  link3 int(11) NOT NULL default '0',
  link4 int(11) NOT NULL default '0',
  link5 int(11) NOT NULL default '0',
  link6 int(11) NOT NULL default '0',
  link7 int(11) NOT NULL default '0',
  link8 int(11) NOT NULL default '0',
  link9 int(11) NOT NULL default '0',
  link10 int(11) NOT NULL default '0',
  php text NOT NULL,
  html text NOT NULL,
  article_id int(11) NOT NULL default '0',
  modultyp_id int(5) NOT NULL default '0',
  PRIMARY KEY  (id,re_article_slice_id,article_id,modultyp_id)
) TYPE=MyISAM;

#
# Dumping data for table `rex_article_slice`
#

# --------------------------------------------------------

#
# Table structure for table `rex_article_type`
#

DROP TABLE IF EXISTS rex_article_type;
CREATE TABLE rex_article_type (
  type_id int(5) NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  description text NOT NULL,
  PRIMARY KEY  (type_id)
) TYPE=MyISAM;

#
# Dumping data for table `rex_article_type`
#

# --------------------------------------------------------

#
# Table structure for table `rex_category`
#

DROP TABLE IF EXISTS rex_category;
CREATE TABLE rex_category (
  id int(11) NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  description text NOT NULL,
  func text NOT NULL,
  re_category_id int(11) NOT NULL default '0',
  prior int(10) NOT NULL default '0',
  path varchar(255) NOT NULL default '',
  status tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (id,re_category_id,prior,path,status)
) TYPE=MyISAM;

#
# Dumping data for table `rex_category`
#

# --------------------------------------------------------

#
# Table structure for table `rex_email`
#

DROP TABLE IF EXISTS rex_email;
CREATE TABLE rex_email (
  email_id int(11) NOT NULL auto_increment,
  sex char(1) NOT NULL default '',
  name varchar(255) NOT NULL default '',
  firstname varchar(255) NOT NULL default '',
  email varchar(255) NOT NULL default '',
  ustamp int(11) NOT NULL default '0',
  PRIMARY KEY  (email_id)
) TYPE=MyISAM;

#
# Dumping data for table `rex_email`
#

# --------------------------------------------------------

#
# Table structure for table `rex_file`
#

DROP TABLE IF EXISTS rex_file;
CREATE TABLE rex_file (
  file_id int(11) NOT NULL auto_increment,
  re_file_id int(11) NOT NULL default '0',
  filetype varchar(255) NOT NULL default '',
  filename varchar(255) NOT NULL default '',
  originalname varchar(255) NOT NULL default '',
  filesize varchar(255) NOT NULL default '',
  width int(11) NOT NULL default '0',
  height int(11) NOT NULL default '0',
  title varchar(255) NOT NULL default '',
  description text NOT NULL,
  copyright varchar(255) NOT NULL default '',
  stamp int(11) NOT NULL default '0',
  PRIMARY KEY  (file_id)
) TYPE=MyISAM;

#
# Dumping data for table `rex_file`
#

# --------------------------------------------------------

#
# Table structure for table `rex_help`
#

DROP TABLE IF EXISTS rex_help;
CREATE TABLE rex_help (
  id int(11) NOT NULL auto_increment,
  page varchar(255) NOT NULL default '',
  name varchar(255) NOT NULL default '',
  description text NOT NULL,
  comment text NOT NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Dumping data for table `rex_help`
#

# --------------------------------------------------------

#
# Table structure for table `rex_modultyp`
#

DROP TABLE IF EXISTS rex_modultyp;
CREATE TABLE rex_modultyp (
  id int(5) NOT NULL auto_increment,
  label varchar(255) NOT NULL default '',
  name varchar(255) NOT NULL default '',
  category_id int(11) NOT NULL default '0',
  ausgabe text NOT NULL,
  bausgabe text NOT NULL,
  eingabe text NOT NULL,
  func text NOT NULL,
  php_enable tinyint(1) NOT NULL default '0',
  html_enable tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (id,category_id,php_enable,html_enable)
) TYPE=MyISAM;

#
# Dumping data for table `rex_modultyp`
#

# --------------------------------------------------------

#
# Table structure for table `rex_template`
#

DROP TABLE IF EXISTS rex_template;
CREATE TABLE rex_template (
  id int(5) NOT NULL auto_increment,
  label varchar(255) NOT NULL default '',
  name varchar(255) NOT NULL default '',
  content text NOT NULL,
  bcontent text NOT NULL,
  active tinyint(1) NOT NULL default '0',
  date timestamp(8) NOT NULL,
  PRIMARY KEY  (id)
) TYPE=MyISAM;

#
# Dumping data for table `rex_template`
#

# --------------------------------------------------------

#
# Table structure for table `rex_user`
#

DROP TABLE IF EXISTS rex_user;
CREATE TABLE rex_user (
  user_id int(11) NOT NULL auto_increment,
  name varchar(255) NOT NULL default '',
  description text NOT NULL,
  login varchar(50) NOT NULL default '',
  psw varchar(50) NOT NULL default '',
  status varchar(5) NOT NULL default '',
  rights text NOT NULL,
  login_tries tinyint(4) NOT NULL default '0',
  createdate int(11) NOT NULL default '0',
  updatedate int(11) NOT NULL default '0',
  lasttrydate int(11) NOT NULL default '0',
  session_id varchar(255) NOT NULL default '',
  PRIMARY KEY  (user_id)
) TYPE=MyISAM;

#
# Dumping data for table `rex_user`
#


# ACTION

DROP TABLE IF EXISTS rex_action;
CREATE TABLE `rex_action` ( `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `name` VARCHAR(255) NOT NULL, `action` TEXT NOT NULL, `prepost` TINYINT NOT NULL, `status` TINYINT NOT NULL );

DROP TABLE IF EXISTS rex_module_action;
CREATE TABLE `rex_module_action` ( `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `module_id` INT NOT NULL, `action_id` INT NOT NULL );


## REDAXO 2.7

DROP TABLE IF EXISTS rex_file_category;
CREATE TABLE `rex_file_category` ( `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `name` VARCHAR(255) NOT NULL );
ALTER TABLE `rex_article` ADD `linkname` VARCHAR(255) NOT NULL AFTER `name`;
