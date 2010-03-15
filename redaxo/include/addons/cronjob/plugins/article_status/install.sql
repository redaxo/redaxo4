INSERT 
  INTO `%TABLE_PREFIX%630_cronjobs` 
  SET 
    `name` = 'Artikel-Status',
    `type` = 'rex_cronjob_article_status',
    `parameters` = 'a:0:{}',
    `interval` = '|1|d|',
    `environment` = '|0|1|',
    `status` = 0,
    `createdate` = '%TIME%',
    `createuser` = '%USER%',
    `updatedate` = '%TIME%',
    `updateuser` = '%USER%';