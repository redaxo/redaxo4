CREATE TABLE `rex_62_params` (
  `field_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255),
  `attributes` VARCHAR(255),
  `type` INT UNSIGNED,
  `default` VARCHAR(255),
  `params` VARCHAR(255),
  `validate` VARCHAR(255),
    UNIQUE (
      `name`
	)
);

CREATE TABLE rex_62_type (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `label` VARCHAR(255),
  `dbtype` VARCHAR(255),
  `dblength` INT(11),
);

INSERT INTO rex_62_type VALUES (1, 'text', 'varchar', 255);
INSERT INTO rex_62_type VALUES (2, 'textarea', 'text', 0);
INSERT INTO rex_62_type VALUES (3, 'select', 'varchar', 255);
INSERT INTO rex_62_type VALUES (4, 'radio', 'varchar', 255);
INSERT INTO rex_62_type VALUES (5, 'checkbox', 'varchar', 255);
INSERT INTO rex_62_type VALUES (6, 'REX_MEDIA_BUTTON', 'varchar', 255);
INSERT INTO rex_62_type VALUES (7, 'REX_MEDIALIST_BUTTON', 'varchar', 255);
INSERT INTO rex_62_type VALUES (8, 'REX_LINK_BUTTON', 'varchar', 255);