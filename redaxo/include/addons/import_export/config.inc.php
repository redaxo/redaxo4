<?php

$mypage = "import_export";        // only for this file

if (!$REX['GG']) $I18N_IM_EXPORT = new i18n($REX['LANG'],$REX['INCLUDE_PATH']."/addons/$mypage/lang");   // CREATE LANG OBJ FOR THIS ADDON

$REX['ADDON']['rxid'][$mypage] = "1";     // unique id /
$REX['ADDON']['page'][$mypage] = "$mypage";     // pagename/foldername
$REX['ADDON']['name'][$mypage] = "Import/Export";   // name
$REX['ADDON']['perm'][$mypage] = "import[]";    // permission

$REX['PERM'][] = "import[]";

// IF NECESSARY INCLUDE FUNC/CLASSES ETC
// INCLUDE IN FRONTEND --- if ($REX[GG]) 
// INCLUDE IN BACKEND --- if (!$REX[GG]) 

?>