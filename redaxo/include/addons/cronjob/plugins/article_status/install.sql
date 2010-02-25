INSERT 
  INTO `%TABLE_PREFIX%630_cronjobs` 
  SET 
    `name` = 'Artikel-Status', 
    `type` = 4, 
    `content` = 'rex_cronjob_article_status', 
    `interval` = '|1|d|', 
    `interval_sec` = 86400, 
    `environment` = '|0|1|',
    `status` = 0,
    `createdate` = '%TIME%',
    `createuser` = '%USER%',
    `updatedate` = '%TIME%',
    `updateuser` = '%USER%';