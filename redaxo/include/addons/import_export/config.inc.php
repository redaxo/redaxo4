<?php

$mypage = "import_export"; 				// only for this file

$I18N_ADDON = new i18n($REX[LANG],$REX[INCLUDE_PATH]."/addons/$mypage/lang/"); 	// CREATE LANG OBJ FOR THIS ADDON

$REX[ADDON][rxid][$mypage] = "REX_001";			// unique id /
// $REX[ADDON][nsid][$mypage] = "REX002,REX003";	// necessary rxid; - not yet included
$REX[ADDON][page][$mypage] = "$mypage";			// pagename/foldername
$REX[ADDON][name][$mypage] = "Import/Export";		// name
$REX[ADDON][perm][$mypage] = "import[]"; 		// permission


$REX[PERM][] = "import[]";

// IF NECESSARY INCLUDE FUNC/CLASSES ETC
// INCLUDE IN FRONTEND --- if ($REX[GG]) 
// INCLUDE IN BACKEND --- if (!$REX[GG]) 

?>