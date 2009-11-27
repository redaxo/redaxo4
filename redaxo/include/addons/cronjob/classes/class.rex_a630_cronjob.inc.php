<?php

/**
 * Cronjob Addon
 *
 * @author gharlan[at]web[dot]de Gregor Harlan
 *
 * @package redaxo4
 * @version svn:$Id$
 */
 
class rex_a630_cronjob
{
  function rex_a630_cronjob()
  {
    
  }
  
  function execute()
  {
    global $REX;
    $environment = $REX['REDAXO'] ? 2 : 1;
    $sql = rex_sql::factory();
    // $sql->debugsql = true;
    $sql->setQuery('
      SELECT    id, name, type, content 
      FROM      '.$REX['TABLE_PREFIX'].'630_cronjobs 
      WHERE     status=1 AND (environment=0 OR environment='.$environment.') AND lasttime+interval_sec <= '.time().' 
      ORDER BY  (lasttime+interval_sec), lasttime 
      LIMIT     1
    ');
    if ($sql->getRows() == 1)
    {
      $id      = $sql->getValue('id');
      $name    = $sql->getValue('name');
      $content = $sql->getValue('content');
      switch($sql->getValue('type'))
      {
        case 1: $success = rex_a630_cronjob::executePhpCode($content);     break;
        case 2: $success = rex_a630_cronjob::executePhpCallback($content); break;
        case 3: $success = rex_a630_cronjob::executeHttpRequest($content); break;
        case 4: $success = rex_a630_cronjob::executeExtension($content);   break;
      }
      rex_a630_cronjob::log($name, $success);
      $sql->setQuery('
        UPDATE  '.$REX['TABLE_PREFIX'].'630_cronjobs 
        SET     lasttime='.time().' 
        WHERE   id='.$id
      );
    }
    rex_a630_cronjob::saveNextTime();
  }
  
  function executePhpCode($code)
  {
    $code = preg_replace('/^\<\?php/','',$code);
    $success = eval($code) !== false;
    return $success;
  }
  
  function executePhpCallback($callback)
  {
    if (preg_match('/^((.*?)\:\:)?(.*?)(\((.*?)\))?\;?$/', $callback, $matches))
    {
      $callback = $matches[3];
      if ($matches[2] != '')
      {
        $callback = array($matches[2], $callback);
      }
      if(!is_callable($callback))
        return false;
      $params = array();
      if($matches[5] != '') 
      {
        $params = explode(',',$matches[5]);
        foreach($params as $i => $param)
        {
          $param = preg_replace('/^(\\\'|\")?(.*?)\\1$/','$2',trim($param));
          $params[$i] = $param;
        }
      }
      return call_user_func_array($callback,$params) !== false;
    }
    return false;
  }
  
  function executeHttpRequest($url)
  {
    if($fh = fopen($url,"r")){ 
      while (!feof($fh)){ 
         fgets($fh); 
      } 
      fclose($fh);
      return true; 
    }
    return false;
  }
  
  function executeExtension($extension)
  {
    global $REX;
    $extensions = rex_register_extension_point('REX_CRONJOB_EXTENSIONS', array());
    if(!isset($extensions[$extension]) || !is_array($extensions[$extension]) || !isset($extensions[$extension][1]))
      return false;
    $callback = $extensions[$extension][1];
    if(!is_callable($callback))
      return false;
    $params = array();
    if(isset($extensions[$extension][2]))
      $params = (array)$extensions[$extension][2];
    return call_user_func_array($callback,$params) !== false;
  }
  
  function log($name, $success)
  {
    global $REX;
    $year = date('Y');
    $month = date('m');
    $dir = $REX['INCLUDE_PATH'].'/addons/cronjob/logs/'.$year;
    $file = $dir.'/'.$year.'-'.$month.'.log';
    if (!is_dir($dir))
    {
      mkdir($dir);
      chmod($dir, $REX['DIRPERM']);
    }
    $content = '';
    if (file_exists($file))
      $content = rex_get_file_contents($file);
    $newline = date('Y-m-d H:i:s  ');
    if ($success)
      $newline .= 'SUCCESS  ';
    else
      $newline .= ' ERROR   ';
    $newline .= $name;
    $content = $newline."\n".$content;
    rex_put_file_contents($file, $content);
  }
  
  function saveNextTime()
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