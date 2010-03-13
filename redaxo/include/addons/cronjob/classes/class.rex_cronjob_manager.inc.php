<?php

/**
 * Cronjob Addon
 *
 * @author gharlan[at]web[dot]de Gregor Harlan
 *
 * @package redaxo4
 * @version svn:$Id$
 */

class rex_cronjob_manager
{ 
  /*public static*/ function check()
  {
    global $REX;
    $environment = (int)$REX['REDAXO'];
    $sql = rex_sql::factory();
    // $sql->debugsql = true;
    $sql->setQuery('
      SELECT    id, name, type, parameters, interval_sec 
      FROM      '. $REX['TABLE_PREFIX'] .'630_cronjobs 
      WHERE     status=1 AND environment LIKE "%|'. $environment .'|%" AND nexttime <= '. time() .' 
      ORDER BY  nexttime ASC, interval_sec DESC 
      LIMIT     1
    ');
    if ($sql->getRows() == 1)
    {
      $id       = $sql->getValue('id');
      $name     = $sql->getValue('name');
      $type     = $sql->getValue('type');
      $params   = unserialize($sql->getValue('parameters'));
      $interval = $sql->getValue('interval_sec');

      $cronjob = rex_cronjob::factory($type);
      rex_cronjob_manager::tryExecute($cronjob, $name, $params);
      
      $time = time();
      $timezone_diff = mktime(0,0,0,1,1,1970);
      $nexttime = $time + $interval - (($time - $timezone_diff) % $interval);
      $sql->setQuery('
        UPDATE  '. $REX['TABLE_PREFIX'] .'630_cronjobs 
        SET     nexttime='. $nexttime .' 
        WHERE   id='. $id
      );
    }
    rex_cronjob_manager::saveNextTime();
  }
  
  /*public static*/ function tryExecute($cronjob, $name = '', $params = array())
  {
    global $REX;
    
    $success = rex_cronjob::isValid($cronjob);
    if($success) 
    {
      $type = $cronjob->getType();
      foreach($params as $key => $value)
        $cronjob->setParam(str_replace($type.'_', '', $key), $value);
      $success = $cronjob->execute();
      if(!$name)
      {
        if($REX['REDAXO'])
          $name = $cronjob->getName();
        else
          $name = $type;
      }
    }
    if (!$name)
      $name = '[no name]';
    
    rex_cronjob_log::save($name, $success);
    
    return $success;
  }
  
  /*public static*/ function saveNextTime()
  {
    global $REX;
    $nexttime = 0;
    $sql = rex_sql::factory();
    // $sql->debugsql = true;
    $sql->setQuery('
      SELECT  MIN(nexttime) AS nexttime
      FROM    '. $REX['TABLE_PREFIX'] .'630_cronjobs 
      WHERE   status=1
    ');
    if ($sql->getRows() != 0 && $sql->getValue('nexttime') !== null)
      $nexttime = max(1,$sql->getValue('nexttime'));
    if ($nexttime != $REX['ADDON']['nexttime']['cronjob']) 
    {
      $content = '$REX[\'ADDON\'][\'nexttime\'][\'cronjob\'] = "'.addslashes($nexttime).'";';
      $file = $REX['INCLUDE_PATH'] .'/addons/cronjob/config.inc.php';
      return (boolean)rex_replace_dynamic_contents($file, $content);
    }
    return false;
  }
  
  /*public static*/ function getTypes()
  {
    $types = array();
    $types[] = 'rex_cronjob_phpcode';
    $types[] = 'rex_cronjob_phpcallback';
    $types[] = 'rex_cronjob_urlrequest';
    
    // ----- EXTENSION POINT
    $types = rex_register_extension_point('REX_CRONJOB_TYPES', $types);

    return $types;
  }
  
  /*public static*/ function registerExtension($params)
  {
    $params['subject'][] = $params['class'];
    
    return $params['subject'];
  }
}