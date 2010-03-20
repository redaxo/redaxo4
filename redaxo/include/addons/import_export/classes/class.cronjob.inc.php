<?php

class rex_cronjob_export extends rex_cronjob
{ 
  /*public*/ function execute()
  {
    global $REX;
    include_once $REX['INCLUDE_PATH'] .'/addons/import_export/functions/function_import_export.inc.php';
    include_once $REX['INCLUDE_PATH'] .'/addons/import_export/functions/function_import_folder.inc.php';
    
    $file = getImportDir() .'/rex_'. $REX['VERSION'] .'_'. date("Ymd");
    $ext = '.sql';
    if (file_exists($file . $ext))
    {
      $i = 1;
      while (file_exists($file .'_'. $i . $ext)) $i++;
      $file = $file .'_'. $i;
    }
    
    return rex_a1_export_db($file . $ext);
  }
  
  /*public*/ function getTypeName()
  {
    global $I18N;
    return $I18N->msg('im_export_database_export');
  }
  
  /*public*/ function getEnvironments()
  {
    return array('backend');
  }
}