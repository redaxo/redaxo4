<?php

/**
 * Version
 *
 * @author jan@kristinus.de
 *
 * @package redaxo4
 * @version $Id: install.inc.php,v 1.13 2008/03/26 21:06:37 kills Exp $
 */

/*
$create_sql = new rex_sql();
$create_sql->setQuery('CREATE TABLE `rex_version_article` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`article_id` INT NOT NULL ,
`version_id` INT NOT NULL ,
`createdate` INT NOT NULL ,
`createuser` VARCHAR( 255 ) NOT NULL ,
`liveversion` INT NOT NULL ,
) ENGINE = MYISAM ;');
*/

$create_sql->setQuery('UPDATE rex_article_slice set revision=0 where revision<1 or revision IS NULL');
$create_sql->setQuery('UPDATE rex_article set revision=0 where revision<1 or revision IS NULL');

$REX['ADDON']['install']['version'] = 1;
// ERRMSG IN CASE: $REX['ADDON']['installmsg']['url_rewrite'] = "Leider konnte nichts installiert werden da.";