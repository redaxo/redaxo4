<?php

/**
 * Cronjob Addon
 *
 * @author gharlan[at]web[dot]de Gregor Harlan
 *
 * @package redaxo4
 * @version svn:$Id$
 */

class rex_cronjob_log
{
  /*public static*/ function getYears()
  {
    $folder = REX_CRONJOB_LOG_FOLDER;
    $years = array ();
  
    $hdl = opendir($folder);
    if($hdl)
    {
      while (($file = readdir($hdl)) !== false)
      {
        if (substr($file, 0, 1) != '.' && is_dir($folder . $file .'/.'))
        {
          $years[] = $file;
        }
      }
      closedir($hdl);
      
      // Sortiere Array
      sort($years);
    }
  
    return $years;  
  }
  
  /*public static*/ function getMonths($year)
  {
    $folder = REX_CRONJOB_LOG_FOLDER;
    $months = array();
    foreach(glob($folder . $year .'/'. $year .'-*.log') as $file)
    {
      $month = substr($file, -6, 2);
      $months[] = $month;
    }
    return $months;
  }
  
  /*public static*/ function getYearMonthArray()
  {
    $array = array();
    foreach(rex_cronjob_log::getYears() as $year)
    {
      $months = rex_cronjob_log::getMonths($year);
      if (!empty($months))
        $array[$year] = $months;
    }
    return $array;
  }
  
  /*public static*/ function getLogOfMonth($month, $year)
  {
    $file = REX_CRONJOB_LOG_FOLDER . $year .'/'. $year .'-'. $month .'.log';
    return rex_get_file_contents($file);
  }
  
  /*public static*/ function getNewestMessages($limit = 10)
  {
    $array = array_reverse(rex_cronjob_log::getYearMonthArray(),true);
    $messages = array();
    foreach($array as $year => $months)
    {
      $months = array_reverse($months,true);
      foreach($months as $month)
      {
        $lines = explode("\n", trim(rex_cronjob_log::getLogOfMonth($month, $year)));
        
        $end = min($limit - count($messages), count($lines));
        for($i = 0; $i < $end; $i++)
          $messages[] = $lines[$i];
        
        if (count($messages) >= $limit)
          break 2;
      }
    }
    return $messages;
  }
  
  /*public static*/ function save($name, $success)
  {
    global $REX;
    
    $year = date('Y');
    $month = date('m');
    
    // Im Frontend ist die Klasse rex_formatter nicht verfuegbar.
    // Falls die Klasse hier manuell eingebunden wird,
    // als Format nicht 'datetime' verwenden, da im Frontend kein I18N-Objekt verfuegbar ist
    $newline = date('Y-m-d H:i');
    
    if ($success)
      $newline .= '  SUCCESS  ';
    else
      $newline .= '   ERROR   ';
      
    $newline .= $name;
    
    $dir = REX_CRONJOB_LOG_FOLDER . $year;
    if (!is_dir($dir))
    {
      mkdir($dir);
      chmod($dir, $REX['DIRPERM']);
    }
    
    $content = '';
    $file = $dir .'/'. $year .'-'. $month .'.log';
    if (file_exists($file))
      $content = rex_get_file_contents($file);
    
    $content = $newline ."\n". $content;
    
    return rex_put_file_contents($file, $content);
  }
}