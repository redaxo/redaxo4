## Redaxo Database Dump Version 3
## Prefix rex_

ALTER TABLE `rex_template` DROP `bcontent`;
ALTER TABLE `rex_template` DROP `date`;
ALTER TABLE `rex_template` ADD `attributes` TEXT NOT NULL;
ALTER TABLE `rex_article` DROP `cattype`;
ALTER TABLE `rex_article` ADD `label` VARCHAR(255) NOT NULL;
ALTER TABLE `rex_article` ADD `url` TEXT NOT NULL;
ALTER TABLE `rex_article` DROP `alias`;
ALTER TABLE `rex_article` CHANGE `attribute` `attributes` TEXT NOT NULL;
ALTER TABLE `rex_article` DROP `online_from`;
ALTER TABLE `rex_article` DROP `online_to`;
ALTER TABLE `rex_article` DROP `fe_user`;
ALTER TABLE `rex_article` DROP `fe_group`;
ALTER TABLE `rex_article` DROP `fe_ext`;
ALTER TABLE `rex_article` DROP `teaser`;
ALTER TABLE `rex_article` DROP `type_id`;
ALTER TABLE `rex_modultyp` DROP `bausgabe`;
ALTER TABLE `rex_modultyp` DROP `func`;
ALTER TABLE `rex_modultyp` DROP `php_enable`;
ALTER TABLE `rex_modultyp` DROP `html_enable`;
ALTER TABLE `rex_modultyp` DROP `perm_category`;
ALTER TABLE `rex_modultyp` DROP `label`;
ALTER TABLE `rex_modultyp` ADD `attributes` TEXT NOT NULL;
ALTER TABLE `rex_file_category` DROP `hide`;
ALTER TABLE `rex_file_category` ADD `attributes` TEXT NOT NULL;
ALTER TABLE `rex_file` ADD `attributes` TEXT NOT NULL AFTER `category_id`; 
ALTER TABLE `rex_article_slice` CHANGE `link1` `link1` VARCHAR(10) NOT NULL, CHANGE `link2` `link2` VARCHAR(10) NOT NULL, CHANGE `link3` `link3` VARCHAR(10) NOT NULL, CHANGE `link4` `link4` VARCHAR(10) NOT NULL, CHANGE `link5` `link5` VARCHAR(10) NOT NULL, CHANGE `link6` `link6` VARCHAR(10) NOT NULL, CHANGE `link7` `link7` VARCHAR(10) NOT NULL, CHANGE `link8` `link8` VARCHAR(10) NOT NULL, CHANGE `link9` `link9` VARCHAR(10) NOT NULL, CHANGE `link10` `link10` VARCHAR(10) NOT NULL;
ALTER TABLE `rex_action` ADD `preview` TEXT NOT NULL, ADD `presave` TEXT NOT NULL, ADD `postsave` TEXT NOT NULL, ADD `previewmode` TINYINT NOT NULL, ADD `presavemode` TINYINT NOT NULL, ADD `postsavemode` TINYINT NOT NULL;
ALTER TABLE `rex_action` DROP `action`, DROP `prepost`, DROP `sadd`, DROP `sedit`, DROP `sdelete`;
ALTER TABLE `rex_action` ADD `createuser` VARCHAR(255) NOT NULL, ADD `createdate` INT NOT NULL, ADD `updateuser` VARCHAR(255) NOT NULL, ADD `updatedate` INT NOT NULL;
ALTER TABLE `rex_user` ADD `cookiekey` varchar(255);
ALTER TABLE `rex_action` ADD `revision` int(11);
ALTER TABLE `rex_article` ADD `revision` int(11);
ALTER TABLE `rex_article_slice` ADD `revision` int(11);
ALTER TABLE `rex_clang` ADD `revision` int(11);
ALTER TABLE `rex_file` ADD `revision` int(11);
ALTER TABLE `rex_file_category` ADD `revision` int(11);
ALTER TABLE `rex_module_action` ADD `revision` int(11);
ALTER TABLE `rex_module` ADD `revision` int(11);
ALTER TABLE `rex_template` ADD `revision` int(11);
ALTER TABLE `rex_user` ADD `revision` int(11);

UPDATE `rex_user` SET `status`=1;
UPDATE `rex_article_slice` SET `ctype`=`ctype`+1;

RENAME TABLE `rex_modultyp` TO `rex_module`;