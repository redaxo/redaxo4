<?php

/**
 * Cronjob Addon
 *
 * @author gharlan[at]web[dot]de Gregor Harlan
 *
 * @package redaxo4
 * @version svn:$Id$
 */

class rex_a630_manager
{ 
  /*public static*/ function checkCronjobs()
  {
    global $REX;
    $environment = (int)$REX['REDAXO'];
    $sql = rex_sql::factory();
    // $sql->debugsql = true;
    $sql->setQuery('
      SELECT    id, name, type, content 
      FROM      '.$REX['TABLE_PREFIX'].'630_cronjobs 
      WHERE     status=1 AND environment LIKE "%|'.$environment.'|%" AND lasttime+interval_sec <= '.time().' 
      ORDER BY  (lasttime+interval_sec), lasttime 
      LIMIT     1
    ');
    if ($sql->getRows() == 1)
    {
      $id      = $sql->getValue('id');
      $name    = $sql->getValue('name');
      $content = $sql->getValue('content');
      $type    = $sql->getValue('type');

      $cronjob = rex_a630_cronjob::factory($type, $name);
      if (is_object($cronjob)) {
        $cronjob->execute($content);
        $sql->setQuery('
          UPDATE  '.$REX['TABLE_PREFIX'].'630_cronjobs 
          SET     lasttime='.time().' 
          WHERE   id='.$id
        );
      }
    }
    rex_a630_manager::saveNextTime();
  }
  
  /*public static*/ function saveNextTime()
  {
    global $REX;
    $nexttime = 0;
    $sql = rex_sql::factory();
    // $sql->debugsql = true;
    $sql->setQuery('
      SELECT  id 
      FROM    '.$REX['TABLE_PREFIX'].'630_cronjobs 
      WHERE   status=1 AND lasttime=0 
      LIMIT   1
    ');
    if ($sql->getRows() != 0)
      $nexttime = time();
    else 
    {
      $sql->setQuery('
        SELECT  MIN(lasttime + interval_sec) AS nexttime 
        FROM    '.$REX['TABLE_PREFIX'].'630_cronjobs 
        WHERE   status=1
      ');
      if ($sql->getRows() != 0)
        $nexttime = $sql->getValue('nexttime');
    }
    $content = '$REX["ADDON"]["nexttime"]["cronjob"] = "'.addslashes($nexttime).'";';
    $file = $REX['INCLUDE_PATH'].'/addons/cronjob/config.inc.php';
    return (boolean)rex_replace_dynamic_contents($file, $content);
  }
}