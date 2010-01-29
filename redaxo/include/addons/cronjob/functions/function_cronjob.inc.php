<?php

function rex_a630_log_years($folder)
{
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

function rex_a630_log_files($folder, $year)
{
  return glob($folder.$year.'/'.$year.'-*.log');
}

function rex_a630_log_messages($folder, $year, $limit)
{
  $logfiles = rex_a630_log_files($folder, $year);
  
  $messages = array();
  foreach($logfiles as $filename)
  {
    $fp = fopen($filename, 'r');
    if ($fp)
    {
      while(($line = fgets($fp)) !== false)
      {
        $messages[] = $line;
        $limit--;
        
        if($limit == 0)
        {
          break;
        }
      }
      fclose($fp);
    }
  }
  
  return $messages;
}
