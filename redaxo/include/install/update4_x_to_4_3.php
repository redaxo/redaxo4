<?php

$update = rex_sql::factory();
// $update->setDebug();

$update->setQuery("ALTER TABLE `rex_template` ADD `revision` INT NOT NULL;");

$update->setQuery("ALTER TABLE `rex_action` CHANGE `revision` `revision` INT( 11 ) NOT NULL DEFAULT '0';");
$update->setQuery("ALTER TABLE `rex_article` CHANGE `revision` `revision` INT( 11 ) NOT NULL DEFAULT '0';");
$update->setQuery("ALTER TABLE `rex_article_slice` CHANGE `revision` `revision` INT( 11 ) NOT NULL DEFAULT '0';");
$update->setQuery("ALTER TABLE `rex_clang` CHANGE `revision` `revision` INT( 11 ) NOT NULL DEFAULT '0';");
$update->setQuery("ALTER TABLE `rex_file` CHANGE `revision` `revision` INT( 11 ) NOT NULL DEFAULT '0';");
$update->setQuery("ALTER TABLE `rex_file_category` CHANGE `revision` `revision` INT( 11 ) NOT NULL DEFAULT '0';");
$update->setQuery("ALTER TABLE `rex_module` CHANGE `revision` `revision` INT( 11 ) NOT NULL DEFAULT '0';");
$update->setQuery("ALTER TABLE `rex_module_action` CHANGE `revision` `revision` INT( 11 ) NOT NULL DEFAULT '0';");
$update->setQuery("ALTER TABLE `rex_user` CHANGE `revision` `revision` INT( 11 ) NOT NULL DEFAULT '0';");
$update->setQuery("ALTER TABLE `rex_template` CHANGE `revision` `revision` INT( 11 ) NOT NULL DEFAULT '0';");

$update->setQuery("UPDATE `rex_article` SET `revision` = 0;");
$update->setQuery("UPDATE `rex_article_slice` SET `revision` = 0;");

$update->setQuery("ALTER TABLE rex_article ADD INDEX `id` (`id`), ADD INDEX `clang` (`clang`), ADD UNIQUE INDEX `find_articles` (`id`, `clang`), ADD INDEX `re_id` (`re_id`);");
$update->setQuery("ALTER TABLE rex_article_slice ADD INDEX `id` (`id`), ADD INDEX `clang` (`clang`), ADD INDEX `re_article_slice_id` (`re_article_slice_id`), ADD INDEX `article_id` (`article_id`), ADD INDEX `find_slices` (`clang`, `article_id`);");
$update->setQuery("ALTER TABLE rex_file ADD INDEX `re_file_id` (`re_file_id`), ADD INDEX `category_id` (`category_id`);");
$update->setQuery("ALTER TABLE rex_file_category DROP PRIMARY KEY, ADD PRIMARY KEY (`id`), ADD INDEX `re_id` (`re_id`);");
$update->setQuery("ALTER TABLE rex_module DROP PRIMARY KEY, ADD PRIMARY KEY (`id`), ADD INDEX `category_id` (`category_id`);");
$update->setQuery("ALTER TABLE rex_user ADD UNIQUE INDEX `login` (`login`(50));");

// $update->setQuery("");

?>