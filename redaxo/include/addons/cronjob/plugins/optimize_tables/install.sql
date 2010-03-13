INSERT 
  INTO `%TABLE_PREFIX%630_cronjobs` 
  SET 
    `name` = 'Tabellen-Optimierung', 
    `type` = 'rex_cronjob_optimize_tables', 
    `parameters` = 'a:0:{}', 
    `interval` = '|1|d|', 
    `interval_sec` = 86400, 
    `environment` = '|0|1|',
    `status` = 0,
    `createdate` = '%TIME%',
    `createuser` = '%USER%',
    `updatedate` = '%TIME%',
    `updateuser` = '%USER%';