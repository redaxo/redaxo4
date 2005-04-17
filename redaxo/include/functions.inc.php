<?php

// ----------------- TIMER
include_once $REX[INCLUDE_PATH]."/functions/function_time.inc.php";

// ----------------- REGISTER GLOBALS CHECK
if (!ini_get('register_globals'))
{
        // register_globals = off;
        if ($_COOKIE) extract($_COOKIE);
        if ($_ENV) extract($_ENV);
        if ($_FILES) extract($_FILES);
        if ($_GET) extract($_GET);
        if ($_POST) extract($_POST);
        if ($_SERVER) extract($_SERVER);
        if ($_SESSION) extract($_SESSION);
}else
{
        // register_globals = on;
}

// ----------------- MAGIC QUOTES CHECK
if (!get_magic_quotes_gpc()) include $REX[INCLUDE_PATH]."/functions/function_rex_mquotes.inc.php";

// ----------------- REX PERMS
$REX[PERM][] = "addon[]";
$REX[PERM][] = "specials[]";
$REX[PERM][] = "mediapool[]";
$REX[PERM][] = "module[]";
$REX[PERM][] = "template[]";
$REX[PERM][] = "user[]";
$REX[PERM][] = "stats[]";



// ----------------- REDAXO INCLUDES
include_once $REX[INCLUDE_PATH]."/classes/class.i18n.inc.php"; // LANGUAGE
include_once $REX[INCLUDE_PATH]."/classes/class.sql.inc.php";
include_once $REX[INCLUDE_PATH]."/classes/class.select.inc.php";
include_once $REX[INCLUDE_PATH]."/classes/class.article.inc.php";
include_once $REX[INCLUDE_PATH]."/classes/class.login.inc.php";
include_once $REX[INCLUDE_PATH]."/classes/class.stat.inc.php";
include_once $REX[INCLUDE_PATH]."/classes/class.cache.inc.php"; // Advanced Caching class
include_once $REX[INCLUDE_PATH]."/classes/class.mime_mail.inc.php";
include_once $REX[INCLUDE_PATH]."/classes/class.oocategory.inc.php"; // OO Classes
include_once $REX[INCLUDE_PATH]."/functions/function_date.inc.php";
include_once $REX[INCLUDE_PATH]."/functions/function_mail.inc.php";
include_once $REX[INCLUDE_PATH]."/functions/function_rex_mediapool.inc.php";
include_once $REX[INCLUDE_PATH]."/functions/function_rex_modrewrite.inc.php";
include_once $REX[INCLUDE_PATH]."/functions/function_rex_title.inc.php";
include_once $REX[INCLUDE_PATH]."/functions/function_rex_generate.inc.php";
include_once $REX[INCLUDE_PATH]."/functions/function_string.inc.php";
include_once $REX[INCLUDE_PATH]."/functions/function_folder.inc.php";
include_once $REX[INCLUDE_PATH]."/functions/function_image.inc.php";
include_once $REX[INCLUDE_PATH]."/addons.inc.php";
include_once $REX[INCLUDE_PATH]."/ctype.inc.php";
include_once $REX[INCLUDE_PATH]."/clang.inc.php";


// ----------------- SET CLANG
if ($REX[CLANG][$clang]=="") $REX[CUR_CLANG] = 0;
else $REX[CUR_CLANG] = $clang;


// ----------------- CREATE LANG OBJ
if (!$REX[GG])
{ 
	if ($lang != "de_de") $lang = "en_gb";
	$REX[LANG] = $lang;
	$I18N = new i18n($REX[LANG],$REX[INCLUDE_PATH]."/lang/");
	$REX[LOCALES] = i18n::getLocales($REX[INCLUDE_PATH]."/lang/");
	setlocale(LC_ALL,trim($I18N->msg("setlocale")));
}

?>