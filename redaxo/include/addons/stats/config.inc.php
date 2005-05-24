<?php

$mypage = "stats"; // only for this file

include_once $REX[INCLUDE_PATH]."/addons/$mypage/classes/class.stats.inc.php";

if (!$REX[GG])
{
	// only backend

 	// CREATE LANG OBJ FOR THIS ADDON
	$I18N_STATS = new i18n($REX[LANG],$REX[INCLUDE_PATH]."/addons/$mypage/lang/");

	$REX[ADDON][rxid][$mypage] = "7"; // unique redaxo addon id
	$REX[ADDON][page][$mypage] = "$mypage";
	$REX[ADDON][name][$mypage] = $I18N_STATS->msg("stats_title");
	$REX[ADDON][perm][$mypage] = "stats[]";
	$REX[PERM][] = "stats[]";

}else
{
	$log = new stats;
	$log->writeLog(($article_id+0));
}

// backend and frontend

$REX[ADDON][tbl][log][$mypage] = "rex_7_log"; // wir noch nicht benutzt

?>