<?php

$mypage = 'import_export';

define('REX_A1_IMPORT_ARCHIVE', 1);
define('REX_A1_IMPORT_DB', 2);
define('REX_A1_IMPORT_EVENT_PRE', 3);
define('REX_A1_IMPORT_EVENT_POST', 4);

if($REX['REDAXO'] && is_object($REX["USER"]))
{
	$I18N->appendFile($REX['INCLUDE_PATH'].'/addons/'.$mypage.'/lang/');

	$REX['ADDON']['rxid'][$mypage] = '1';
	$REX['ADDON']['page'][$mypage] = $mypage;
	$REX['ADDON']['name'][$mypage] = $I18N->msg("im_export_importexport");
	$REX['ADDON']['perm'][$mypage] = 'import_export[export]';
	$REX['ADDON']['version'][$mypage] = "1.3";
	$REX['ADDON']['author'][$mypage] = "Jan Kristinus";
	$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';
	
	$REX['PERM'][] = 'import_export[export]';
	$REX['PERM'][] = 'import_export[import]';
	
	$REX['ADDON'][$mypage]['SUBPAGES'] = array();
	
 	if($REX["USER"]->hasPerm('import_export[import]') || $REX["USER"]->isAdmin())
 	{
		$REX['ADDON'][$mypage]['SUBPAGES'][] = array ('import', $I18N->msg('im_export_import'));
 	}
	$REX['ADDON'][$mypage]['SUBPAGES'][] = array ('', $I18N->msg('im_export_export'));
}

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
    array($file.$ext)
  );
  
  return $params['subject'];
}