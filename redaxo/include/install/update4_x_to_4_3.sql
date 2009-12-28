ALTER TABLE rex_article ADD INDEX `id` (`id`), ADD INDEX `clang` (`clang`), ADD UNIQUE INDEX `find_articles` (`id`, `clang`), ADD INDEX `re_id` (`re_id`);
ALTER TABLE rex_article_slice ADD INDEX `id` (`id`), ADD INDEX `clang` (`clang`), ADD INDEX `re_article_slice_id` (`re_article_slice_id`), ADD INDEX `article_id` (`article_id`), ADD INDEX `find_slices` (`clang`, `article_id`);
ALTER TABLE rex_file ADD INDEX `re_file_id` (`re_file_id`), ADD INDEX `category_id` (`category_id`);
ALTER TABLE rex_file_category DROP PRIMARY KEY, ADD PRIMARY KEY (`id`), ADD INDEX `re_id` (`re_id`);
ALTER TABLE rex_module DROP PRIMARY KEY, ADD PRIMARY KEY (`id`), ADD INDEX `category_id` (`category_id`);
ALTER TABLE rex_user ADD UNIQUE INDEX `login` (`login`(50)); 