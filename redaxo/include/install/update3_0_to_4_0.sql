## Redaxo Database Dump Version 4
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
ALTER TABLE `rex_article` DROP `description`;
ALTER TABLE `rex_article` DROP `keywords`;
ALTER TABLE `rex_article` DROP `file`;
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
ALTER TABLE `rex_article_slice` CHANGE `value1` text NULL  , `value2` text NULL  , `value3` text NULL  , `value4` text NULL  , `value5` text NULL  , `value6` text NULL  , `value7` text NULL  , `value8` text NULL  , `value9` text NULL  , `value10` text NULL  , `value11` text NULL  , `value12` text NULL  , `value13` text NULL  , `value14` text NULL  , `value15` text NULL  , `value16` text NULL  , `value17` text NULL  , `value18` text NULL  , `value19` text NULL  , `value20` text NULL  , `file1` varchar(255) NULL  , `file2` varchar(255) NULL  , `file3` varchar(255) NULL  , `file4` varchar(255) NULL  , `file5` varchar(255) NULL  , `file6` varchar(255) NULL  , `file7` varchar(255) NULL  , `file8` varchar(255) NULL  , `file9` varchar(255) NULL  , `file10` varchar(255) NULL  , `filelist1` text NULL  , `filelist2` text NULL  , `filelist3` text NULL  , `filelist4` text NULL  , `filelist5` text NULL  , `filelist6` text NULL  , `filelist7` text NULL  , `filelist8` text NULL  , `filelist9` text NULL  , `filelist10` text NULL  , `link1` varchar(10) NULL  , `link2` varchar(10) NULL  , `link3` varchar(10) NULL  , `link4` varchar(10) NULL  , `link5` varchar(10) NULL  , `link6` varchar(10) NULL  , `link7` varchar(10) NULL  , `link8` varchar(10) NULL  , `link9` varchar(10) NULL  , `link10` varchar(10) NULL  , `linklist1` text NULL  , `linklist2` text NULL  , `linklist3` text NULL  , `linklist4` text NULL  , `linklist5` text NULL  , `linklist6` text NULL  , `linklist7` text NULL  , `linklist8` text NULL  , `linklist9` text NULL  , `linklist10` text NULL  , `php` text NULL  , `html` text NULL
ALTER TABLE `rex_action` ADD `preview` TEXT NOT NULL, ADD `presave` TEXT NOT NULL, ADD `postsave` TEXT NOT NULL, ADD `previewmode` TINYINT NOT NULL, ADD `presavemode` TINYINT NOT NULL, ADD `postsavemode` TINYINT NOT NULL;
ALTER TABLE `rex_action` DROP `action`, DROP `prepost`, DROP `sadd`, DROP `sedit`, DROP `sdelete`;
ALTER TABLE `rex_action` ADD `createuser` VARCHAR(255) NOT NULL, ADD `createdate` INT NOT NULL, ADD `updateuser` VARCHAR(255) NOT NULL, ADD `updatedate` INT NOT NULL;
ALTER TABLE `rex_user` ADD `cookiekey` varchar(255);
ALTER TABLE `rex_article_slice` ADD `next_article_slice_id` int(11);
ALTER TABLE `rex_action` ADD `revision` int(11);
ALTER TABLE `rex_article` ADD `revision` int(11);
ALTER TABLE `rex_article_slice` ADD `revision` int(11);
ALTER TABLE `rex_clang` ADD `revision` int(11);
ALTER TABLE `rex_file` DROP `copyright`;
ALTER TABLE `rex_file` DROP `description`;
ALTER TABLE `rex_file` ADD `revision` int(11);
ALTER TABLE `rex_file_category` ADD `revision` int(11);
ALTER TABLE `rex_module_action` ADD `revision` int(11);
ALTER TABLE `rex_modultyp` ADD `revision` int(11);
ALTER TABLE `rex_template` ADD `revision` int(11);
ALTER TABLE `rex_user` ADD `revision` int(11);

UPDATE `rex_user` SET `status`=1;
UPDATE `rex_article_slice` SET `ctype`=`ctype`+1;

RENAME TABLE `rex_modultyp` TO `rex_module`;