<?php

class rex_a1_cronjob extends rex_cronjob
{ 
  /*protected*/ function _execute()
  {
    global $REX;
    include_once $REX['INCLUDE_PATH'].'/addons/import_export/functions/function_import_export.inc.php';
    include_once $REX['INCLUDE_PATH'].'/addons/import_export/functions/function_import_folder.inc.php';
    
    $file = getImportDir().'/rex_'.$REX['VERSION'].'_'.date("Ymd");
    $ext = '.sql';
    if (file_exists($file.$ext))
    {
      $i = 1;
      while (file_exists($file.'_'.$i.$ext)) $i++;
      $file = $file.'_'.$i;
    }
    
    return rex_a1_export_db($file.$ext);
  }
}