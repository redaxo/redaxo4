<?php

/**
 * Suche Addon
 *
 * @author vscope new media design
 * @author <a href="http://www.vscope.at">www.vscope.at</a>
 *
 */

$mypage = "search_index";                                 // only for this file

//$I18N_SEARCH_INDEX = new i18n($REX[LANG],$REX[INCLUDE_PATH]."/addons/$mypage/lang/");         // CREATE LANG OBJ FOR THIS ADDON

$REX['ADDON']['rxid'][$mypage] = "12";                        // unique id /
$REX['ADDON']['page'][$mypage] = "$mypage";                        // pagename/foldername
$REX['ADDON']['name'][$mypage] = "Such Index";                // name
$REX['ADDON']['perm'][$mypage] = "search_index[]";                 // permission

$REX['PERM'][] = "search_index[]";

include($REX['INCLUDE_PATH']."/addons/$mypage/classes/class.search_index.inc.php");

/*
todos:
CAT_ADDED, CAT_EDITED, ART_ADDED
im backend als EP einbauen
*/

?>