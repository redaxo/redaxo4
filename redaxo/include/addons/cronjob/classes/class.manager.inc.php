<?php

/**
 * Cronjob Addon
 *
 * @author gharlan[at]web[dot]de Gregor Harlan
 *
 * @package redaxo4
 * @version svn:$Id$
 */

/*final*/ class rex_cronjob_manager
{ 
  /*public static*/ function check()
  {
    global $REX;
    $environment = (int)$REX['REDAXO'];
    $query = '
      SELECT    id, name, type, parameters, `interval`
      FROM      '. REX_CRONJOB_TABLE .' 
      WHERE     status=1 AND environment LIKE "%|'. $environment .'|%" AND nexttime <= '. time() .' 
      ORDER BY  nexttime ASC
      LIMIT     1
    ';
    rex_cronjob_manager_sql::tryExecute($query);
  }
  
  /*public static*/ function tryExecute($cronjob, $name = '', $params = array(), $log = true)
  {
    global $REX;
    
    $message = '';
    $success = rex_cronjob::isValid($cronjob);
    if(!$success)
    {
      if (is_object($cronjob))
        $message = 'Invalid cronjob class "'. getClass($cronjob) .'"';
      else
        $message = 'Class "'. $cronjob .'" not found';
    }
    else
    {
      $type = $cronjob->getType();
      if (is_array($params))
      {
        foreach($params as $key => $value)
          $cronjob->setParam(str_replace($type.'_', '', $key), $value);
      }
      $success = $cronjob->execute();
      if (is_array($success))
      {
        if (isset($success[1]))
          $message = $success[1];
        $success = $success[0];
      }
      if ($message == '' && !$success)
      {
        $message = 'Unknown error';
      }
      if($log && !$name)
      {
        if($REX['REDAXO'])
          $name = $cronjob->getTypeName();
        else
          $name = $type;
      }
    }
    
    if ($log) 
    {
      if (!$name)
        $name = '[no name]';
      rex_cronjob_log::save($name, $success, $message);
    }
    
    return $success;
  }
  
  /*public static*/ function saveNextTime()
  {
    global $REX;
    $nexttime = rex_cronjob_manager_sql::getMinNextTime();
    if ($nexttime === null)
      $nexttime = 0;
    else
      $nexttime = max(1, $nexttime);
    if ($nexttime != $REX['ADDON']['nexttime']['cronjob']) 
    {
      $content = '$REX[\'ADDON\'][\'nexttime\'][\'cronjob\'] = "'.addslashes($nexttime).'";';
      $file = $REX['INCLUDE_PATH'] .'/addons/cronjob/config.inc.php';
      if (rex_replace_dynamic_contents($file, $content))
      {
        $REX['ADDON']['nexttime']['cronjob'] = $nexttime;
        return true;
      }
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
    $types = rex_register_extension_point('CRONJOB_TYPES', $types);

    return $types;
  }
  
  /*public static*/ function registerExtension($params)
  {
    $params['subject'][] = $params['class'];
    return $params['subject'];
  }
}


/*final*/ class rex_cronjob_manager_sql
{
  /*public static*/ function getName($id)
  {
    $sql = rex_cronjob_manager_sql::_getSqlInstance();
    $sql->setQuery('
      SELECT  name 
      FROM    '. REX_CRONJOB_TABLE .' 
      WHERE   id='. $id .' 
      LIMIT   1
    ');
    if($sql->getRows() == 1)
      return $sql->getValue('name');
    return null;
  }

  /*public static*/ function setStatus($id, $status)
  {
    global $REX;
    $sql = rex_cronjob_manager_sql::_getSqlInstance();
    $sql->setTable(REX_CRONJOB_TABLE);
    $sql->setWhere('id = '. $id);
    $sql->setValue('status', $status);
    $sql->addGlobalUpdateFields();
    $success = $sql->update();
    rex_cronjob_manager::saveNextTime();
    return $success;
  }
  
  /*public static*/ function delete($id)
  {
    $sql = rex_cronjob_manager_sql::_getSqlInstance();
    $sql->setTable(REX_CRONJOB_TABLE);
    $sql->setWhere('id = '. $id);
    $success = $sql->delete();
    rex_cronjob_manager::saveNextTime();
    return $success;
  }
  
  /*public static*/ function tryExecute($query_or_id, $log = true)
  {
    global $REX;
    $sql = rex_cronjob_manager_sql::_getSqlInstance();
    if (is_int($query_or_id))
    {
      $environment = (int)$REX['REDAXO'];
      $sql->setQuery('
        SELECT    id, name, type, parameters, `interval` 
        FROM      '. REX_CRONJOB_TABLE .' 
        WHERE     id='. $query_or_id .' AND environment LIKE "%|'. $environment .'|%" 
        LIMIT     1
      ');
    }
    else
    {
      $sql->setQuery($query_or_id);
    }
    $success = false;
    if ($sql->getRows() == 1)
    {
      $id       = $sql->getValue('id');
      $name     = $sql->getValue('name');
      $type     = $sql->getValue('type');
      $params   = unserialize($sql->getValue('parameters'));
      $interval = $sql->getValue('interval');

      $cronjob = rex_cronjob::factory($type);
      $success = rex_cronjob_manager::tryExecute($cronjob, $name, $params, $log);

      $nexttime = rex_cronjob_manager_sql::_calculateNextTime($interval);
      rex_cronjob_manager_sql::setNextTime($id, $nexttime);
    }
    rex_cronjob_manager::saveNextTime();
    return $success;
  }
  
  /*public static*/ function setNextTime($id, $nexttime)
  {
    $sql = rex_cronjob_manager_sql::_getSqlInstance();
    return $sql->setQuery('
      UPDATE  '. REX_CRONJOB_TABLE .' 
      SET     nexttime='. $nexttime .' 
      WHERE   id='. $id
    );
  }
  
  /*public static*/ function getMinNextTime()
  {
    $sql = rex_cronjob_manager_sql::_getSqlInstance();
    $sql->setQuery('
      SELECT  MIN(nexttime) AS nexttime
      FROM    '. REX_CRONJOB_TABLE .' 
      WHERE   status=1
    ');
    if($sql->getRows() == 1)
      return $sql->getValue('nexttime');
    return null;
  }
  
  /*private static*/ function _getSqlInstance()
  {
    static $sql = null;
    if (!$sql)
    {
      $sql = rex_sql::factory();
      // $sql->debugsql = true;
    }
    return $sql;
  }
  
  /*private static*/ function _calculateNextTime($interval)
  {
    $interval = explode('|', trim($interval, '|'));
    if (is_array($interval) && isset($interval[0]) && isset($interval[1]))
    {
      $date = getdate();
      switch($interval[1])
      {
        case 'h': return mktime($date['hours'] + $interval[0], 0, 0);
        case 'd': return mktime(0, 0, 0, $date['mon'], $date['mday'] + $interval[0]);
        case 'w': return mktime(0, 0, 0, $date['mon'], $date['mday'] + $interval[0] * 7 - $date['wday']);
        case 'm': return mktime(0, 0, 0, $date['mon'] + $interval[0], 1);
        case 'y': return mktime(0, 0, 0, 1, 1, $date['year'] + $interval[0]);
      } 
    }
    return null;
  }
}