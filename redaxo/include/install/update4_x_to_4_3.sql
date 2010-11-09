## Redaxo Database Dump Version 4
## Prefix rex_

ALTER TABLE `rex_62_params` CHANGE `validate` `validate` TEXT NULL
ALTER TABLE `rex_62_params` ADD `restrictions` TEXT NOT NULL AFTER `validate`