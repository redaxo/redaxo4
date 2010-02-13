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
      SELECT    id, name, type, content, interval_sec 
      FROM      '.$REX['TABLE_PREFIX'].'630_cronjobs 
      WHERE     status=1 AND environment LIKE "%|'.$environment.'|%" AND nexttime <= '.time().' 
      ORDER BY  nexttime ASC, interval_sec DESC 
      LIMIT     1
    ');
    if ($sql->getRows() == 1)
    {
      $id       = $sql->getValue('id');
      $name     = $sql->getValue('name');
      $content  = $sql->getValue('content');
      $type     = $sql->getValue('type');
      $interval = $sql->getValue('interval_sec');

      $cronjob = rex_a630_cronjob::factory($type, $name);
      if (is_object($cronjob)) {
        if ($cronjob->execute($content)) {
          $nexttime = time() + $interval - (time() % $interval);
          $sql->setQuery('
            UPDATE  '.$REX['TABLE_PREFIX'].'630_cronjobs 
            SET     nexttime='.$nexttime.' 
            WHERE   id='.$id
          );
        }
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
      SELECT  MIN(nexttime) AS nexttime
      FROM    '.$REX['TABLE_PREFIX'].'630_cronjobs 
      WHERE   status=1
    ');
    if ($sql->getRows() != 0)
      $nexttime = min(1,$sql->getValue('nexttime'));
    if ($nexttime != $REX["ADDON"]["nexttime"]["cronjob"]) {
      $content = '$REX["ADDON"]["nexttime"]["cronjob"] = "'.addslashes($nexttime).'";';
      $file = $REX['INCLUDE_PATH'].'/addons/cronjob/config.inc.php';
      return (boolean)rex_replace_dynamic_contents($file, $content);
    }
    return false;
  }
}