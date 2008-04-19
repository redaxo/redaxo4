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
ALTER TABLE `rex_article_slice` CHANGE `value1` `value1` text NULL, CHANGE `value2` `value2` text NULL, CHANGE `value3` `value3` text NULL, CHANGE `value4` `value4` text NULL, CHANGE `value5` `value5` text NULL, CHANGE `value6` `value6` text NULL, CHANGE `value7` `value7` text NULL, CHANGE `value8` `value8` text NULL, CHANGE `value9` `value9` text NULL, CHANGE `value10` `value10` text NULL, CHANGE `value11` `value11` text NULL, CHANGE `value12` `value12` text NULL, CHANGE `value13` `value13` text NULL, CHANGE `value14` `value14` text NULL, CHANGE `value15` `value15` text NULL, CHANGE `value16` `value16` text NULL, CHANGE `value17` `value17` text NULL, CHANGE `value18` `value18` text NULL, CHANGE `value19` `value19` text NULL, CHANGE `value20` `value20` text NULL, CHANGE `file1` `file1` varchar(255) NULL, CHANGE `file2` `file2` varchar(255) NULL, CHANGE `file3` `file3` varchar(255) NULL, CHANGE `file4` `file4` varchar(255) NULL, CHANGE `file5` `file5` varchar(255) NULL, CHANGE `file6` `file6` varchar(255) NULL, CHANGE `file7` `file7` varchar(255) NULL, CHANGE `file8` `file8` varchar(255) NULL, CHANGE `file9` `file9` varchar(255) NULL, CHANGE `file10` `file10` varchar(255) NULL, CHANGE `filelist1` `filelist1` text NULL, CHANGE `filelist2` `filelist2` text NULL, CHANGE `filelist3` `filelist3` text NULL, CHANGE `filelist4` `filelist4` text NULL, CHANGE `filelist5` `filelist5` text NULL, CHANGE `filelist6` `filelist6` text NULL, CHANGE `filelist7` `filelist7` text NULL, CHANGE `filelist8` `filelist8` text NULL, CHANGE `filelist9` `filelist9` text NULL, CHANGE `filelist10` `filelist10` text NULL, CHANGE `link1` `link1` varchar(10) NULL, CHANGE `link2` `link2` varchar(10) NULL, CHANGE `link3` `link3` varchar(10) NULL, CHANGE `link4` `link4` varchar(10) NULL, CHANGE `link5` `link5` varchar(10) NULL, CHANGE `link6` `link6` varchar(10) NULL, CHANGE `link7` `link7` varchar(10) NULL, CHANGE `link8` `link8` varchar(10) NULL, CHANGE `link9` `link9` varchar(10) NULL, CHANGE `link10` `link10` varchar(10) NULL, CHANGE `linklist1` `linklist1` text NULL, CHANGE `linklist2` `linklist2` text NULL, CHANGE `linklist3` `linklist3` text NULL, CHANGE `linklist4` `linklist4` text NULL, CHANGE `linklist5` `linklist5` text NULL, CHANGE `linklist6` `linklist6` text NULL, CHANGE `linklist7` `linklist7` text NULL, CHANGE `linklist8` `linklist8` text NULL, CHANGE `linklist9` `linklist9` text NULL, CHANGE `linklist10` `linklist10` text NULL, CHANGE `php` `php` text NULL, CHANGE `html` `html` text NULL;
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

DROP TABLE `rex_article_type`;