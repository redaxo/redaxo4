CREATE TABLE `rex_file_category` ( `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `name` VARCHAR(255) NOT NULL );
ALTER TABLE `rex_article` ADD `linkname` VARCHAR(255) NOT NULL AFTER `name`;
ALTER TABLE `rex_file` ADD `re_file_id` INT DEFAULT '0' NOT NULL AFTER `file_id`;
ALTER TABLE `rex_file` ADD `category_id` INT NOT NULL AFTER `re_file_id`;
