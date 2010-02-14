<?php

/**
 * Cronjob Addon
 *
 * @author gharlan[at]web[dot]de Gregor Harlan
 *
 * @package redaxo4
 * @version svn:$Id$
 */

class rex_a630_log
{
  /*public*/ function getYears()
  {
    $folder = REX_LOG_FOLDER;
    $years = array ();
  
    $hdl = opendir($folder);
    if($hdl)
    {
      while (($file = readdir($hdl)) !== false)
      {
        if (substr($file,0,1) != '.' && is_dir($folder.$file.'/.'))
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
  
  /*public*/ function getMonths($year)
  {
    $folder = REX_LOG_FOLDER;
    $months = array();
    foreach(glob($folder.$year.'/'.$year.'-*.log') as $file)
    {
      $month = substr($file, -6, 2);
      $months[] = $month;
    }
    return $months;
  }
  
  /*public*/ function getYearMonthArray()
  {
    $array = array();
    foreach(rex_a630_log::getYears() as $year)
    {
      $months = rex_a630_log::getMonths($year);
      if (!empty($months))
        $array[$year] = $months;
    }
    return $array;
  }
  
  /*public*/ function getLogOfMonth($month, $year)
  {
    $file = REX_LOG_FOLDER.$year.'/'.$year.'-'.$month.'.log';
    return rex_get_file_contents($file);
  }
  
  /*public*/ function getNewestMessages($limit = 10)
  {
    $array = array_reverse(rex_a630_log::getYearMonthArray(),true);
    $messages = array();
    foreach($array as $year => $months)
    {
      $months = array_reverse($months,true);
      foreach($months as $month)
      {
        $lines = explode("\n",rex_a630_log::getLogOfMonth($month, $year));
        for($i = count($messages); $i < $limit; $i++)
          $messages[] = $lines[$i];
        if (count($messages) >= $limit)
          break 2;
      }
    }
    return $messages;
  }
  
  public function saveLog($newline, $month, $year)
  {
    global $REX;
    
    $dir = REX_LOG_FOLDER.$year;
    if (!is_dir($dir))
    {
      mkdir($dir);
      chmod($dir, $REX['DIRPERM']);
    }
    
    $content = '';
    $file = $dir.'/'.$year.'-'.$month.'.log';
    if (file_exists($file))
      $content = rex_get_file_contents($file);
    
    $content = $newline."\n".$content;
    
    return rex_put_file_contents($file, $content);
  }
}