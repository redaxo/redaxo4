## Redaxo Database Dump Version 4
## Prefix rex_

ALTER TABLE `rex_action` CHANGE `revision` `revision` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `rex_article` CHANGE `revision` `revision` INT( 11 ) NOT NULL DEFAULT '0'; 
ALTER TABLE `rex_article_slice` CHANGE `revision` `revision` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `rex_clang` CHANGE `revision` `revision` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `rex_file` CHANGE `revision` `revision` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `rex_file_category` CHANGE `revision` `revision` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `rex_module` CHANGE `revision` `revision` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `rex_module_action` CHANGE `revision` `revision` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `rex_user` CHANGE `revision` `revision` INT( 11 ) NOT NULL DEFAULT '0';
UPDATE `rex_article` SET `revision` = 0;
UPDATE `rex_article_slice` SET `revision` = 0;
