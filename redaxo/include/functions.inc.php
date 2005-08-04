<?php

// ----------------- TIMER
include_once $REX['INCLUDE_PATH']."/functions/function_time.inc.php";

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
if (!get_magic_quotes_gpc()) include $REX['INCLUDE_PATH']."/functions/function_rex_mquotes.inc.php";

// ----------------- REX PERMS

// ----- allgemein
$REX['PERM'][] = "addon[]";
$REX['PERM'][] = "specials[]";
$REX['PERM'][] = "mediapool[]";
$REX['PERM'][] = "module[]";
$REX['PERM'][] = "template[]";
$REX['PERM'][] = "user[]";

// ----- optionen
$REX['EXTPERM'][] = "advancedMode[]";
$REX['EXTPERM'][] = "caching[]";
$REX['EXTPERM'][] = "moveslice[]";

// ----------------- REDAXO INCLUDES
include_once $REX['INCLUDE_PATH']."/classes/class.i18n.inc.php";
include_once $REX['INCLUDE_PATH']."/classes/class.sql.inc.php";
include_once $REX['INCLUDE_PATH']."/classes/class.select.inc.php";
include_once $REX['INCLUDE_PATH']."/classes/class.article.inc.php";
include_once $REX['INCLUDE_PATH']."/classes/class.login.inc.php";
include_once $REX['INCLUDE_PATH']."/classes/class.cache.inc.php";
include_once $REX['INCLUDE_PATH']."/classes/class.ooredaxo.inc.php";
include_once $REX['INCLUDE_PATH']."/classes/class.oocategory.inc.php";
include_once $REX['INCLUDE_PATH']."/classes/class.ooarticle.inc.php";
include_once $REX['INCLUDE_PATH']."/classes/class.ooarticleslice.inc.php";
include_once $REX['INCLUDE_PATH']."/classes/class.oomediacategory.inc.php";
include_once $REX['INCLUDE_PATH']."/classes/class.oomedia.inc.php";

if (!$REX[GG])
{
	include_once $REX['INCLUDE_PATH']."/functions/function_rex_title.inc.php";
	include_once $REX['INCLUDE_PATH']."/functions/function_rex_generate.inc.php";
}

// ----- EXTRA CLASSES
include_once $REX['INCLUDE_PATH']."/classes/class.textile.inc.php";
include_once $REX['INCLUDE_PATH']."/classes/class.phpmailer.inc.php";
include_once $REX['INCLUDE_PATH']."/classes/class.wysiwyg.inc.php";

// ----- FUNCTIONS
include_once $REX['INCLUDE_PATH']."/functions/function_rex_modrewrite.inc.php";

// ----- EXTRA FUNCTIONS
include_once $REX['INCLUDE_PATH']."/functions/function_rex_wysiwyg.inc.php";
include_once $REX['INCLUDE_PATH']."/functions/function_image.inc.php";
include_once $REX['INCLUDE_PATH']."/functions/function_string.inc.php";
include_once $REX['INCLUDE_PATH']."/functions/function_folder.inc.php";

// ----- CONFIG FILES
include_once $REX['INCLUDE_PATH']."/addons.inc.php";
include_once $REX['INCLUDE_PATH']."/ctype.inc.php";
include_once $REX['INCLUDE_PATH']."/clang.inc.php";

// ----------------- SET CLANG
if ($REX['CLANG'][$clang]=="")
{
	$REX['CUR_CLANG'] = 0;
	$clang = 0;
}else
{
	$REX['CUR_CLANG'] = $clang;
}

?>