<?php

$mypage = 'import_export';        // only for this file

if ($REX['REDAXO'])
  $I18N_IM_EXPORT = new i18n($REX['LANG'],$REX['INCLUDE_PATH'].'/addons/'. $mypage .'/lang');   // CREATE LANG OBJ FOR THIS ADDON

$REX['ADDON']['rxid'][$mypage] = '1';     // unique id /
$REX['ADDON']['page'][$mypage] = $mypage;     // pagename/foldername
$REX['ADDON']['name'][$mypage] = 'Import/Export';   // name
$REX['ADDON']['perm'][$mypage] = 'import[]';    // permission
$REX['ADDON']['version'][$mypage] = "0.9";
$REX['ADDON']['author'][$mypage] = "Jan Kristinus";
// $REX['ADDON']['supportpage'][$mypage] = "";

$REX['PERM'][] = 'import[]';

?>