<?php 

rex_register_extension('REX_CRONJOB_EXTENSIONS','rex_a1_cronjob');

function rex_a1_cronjob($params)
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
  
  $params['subject']['rex_a1_export'] = array(
    'translate:im_export_database_export',
    'rex_a1_export_db',
    array($file.$ext),
    'backend'
  );
  
  return $params['subject'];
}