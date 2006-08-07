<?php
/** 
 * Bindet nötige Klassen/Funktionen ein
 * @package redaxo3 
 * @version $Id$ 
 */ 

// ----------------- TIMER
include_once $REX['INCLUDE_PATH']."/functions/function_rex_time.inc.php";

$REX_TEMP = $REX;

// ----------------- MAGIC QUOTES CHECK
if (!get_magic_quotes_gpc()) include $REX['INCLUDE_PATH']."/functions/function_rex_mquotes.inc.php";

// ----------------- REGISTER GLOBALS CHECK
if (!ini_get('register_globals'))
{
        // register_globals = off;
        
        if (isset($_COOKIE) and $_COOKIE) extract($_COOKIE);
        if (isset($_ENV) and $_ENV) extract($_ENV);
        if (isset($_FILES) and $_FILES) extract($_FILES);
        if (isset($_GET) and $_GET) extract($_GET);
        if (isset($_POST) and $_POST) extract($_POST);
        if (isset($_SERVER) and $_SERVER) extract($_SERVER);
        if (isset($_SESSION) and $_SESSION) extract($_SESSION);
}else
{
        // register_globals = on;
        
}

$REX = $REX_TEMP;

// ----------------- REX PERMS

// ----- allgemein
$REX['PERM'][] = 'addon[]';
$REX['PERM'][] = 'specials[]';
$REX['PERM'][] = 'mediapool[]';
$REX['PERM'][] = 'module[]';
$REX['PERM'][] = 'template[]';
$REX['PERM'][] = 'user[]';

// ----- optionen
$REX['EXTPERM'][] = 'advancedMode[]';
$REX['EXTPERM'][] = 'moveSlice[]';
$REX['EXTPERM'][] = 'copyContent[]';
$REX['EXTPERM'][] = 'moveArticle[]';
$REX['EXTPERM'][] = 'copyArticle[]';
$REX['EXTPERM'][] = 'moveCategory[]';
$REX['EXTPERM'][] = 'publishArticle[]';
$REX['EXTPERM'][] = 'publishCategory[]';

// ----- extras
$REX['EXTRAPERM'][] = 'editContentOnly[]';

// ----- standard variables
$REX['VARIABLES'][] = 'rex_var_globals';
$REX['VARIABLES'][] = 'rex_var_value';
$REX['VARIABLES'][] = 'rex_var_link';
$REX['VARIABLES'][] = 'rex_var_media';

// ----------------- REDAXO INCLUDES
include_once $REX['INCLUDE_PATH'].'/classes/class.i18n.inc.php';
include_once $REX['INCLUDE_PATH'].'/classes/class.sql.inc.php';
include_once $REX['INCLUDE_PATH'].'/classes/class.select.inc.php';
include_once $REX['INCLUDE_PATH'].'/classes/class.article.inc.php';
include_once $REX['INCLUDE_PATH'].'/classes/class.login.inc.php';
include_once $REX['INCLUDE_PATH'].'/classes/class.ooredaxo.inc.php';
include_once $REX['INCLUDE_PATH'].'/classes/class.oocategory.inc.php';
include_once $REX['INCLUDE_PATH'].'/classes/class.ooarticle.inc.php';
include_once $REX['INCLUDE_PATH'].'/classes/class.ooarticleslice.inc.php';
include_once $REX['INCLUDE_PATH'].'/classes/class.oomediacategory.inc.php';
include_once $REX['INCLUDE_PATH'].'/classes/class.oomedia.inc.php';
include_once $REX['INCLUDE_PATH'].'/classes/class.ooaddon.inc.php';

if (!$REX['GG'])
{
  include_once $REX['INCLUDE_PATH'].'/functions/function_rex_title.inc.php';
  include_once $REX['INCLUDE_PATH'].'/functions/function_rex_generate.inc.php';
  include_once $REX['INCLUDE_PATH'].'/classes/class.rex_var.inc.php';
  foreach($REX['VARIABLES'] as $key => $value)
  {
    require_once ($REX['INCLUDE_PATH'].'/classes/variables/class.'.$value.'.inc.php');
    $REX['VARIABLES'][$key] = new $value;
  }
}

// ----- EXTRA CLASSES
include_once $REX['INCLUDE_PATH'].'/classes/class.textile.inc.php';
include_once $REX['INCLUDE_PATH'].'/classes/class.phpmailer.inc.php';

// ----- FUNCTIONS
include_once $REX['INCLUDE_PATH'].'/functions/function_rex_globals.inc.php';
include_once $REX['INCLUDE_PATH'].'/functions/function_rex_modrewrite.inc.php';
include_once $REX['INCLUDE_PATH'].'/functions/function_rex_extension.inc.php';
include_once $REX['INCLUDE_PATH'].'/functions/function_rex_other.inc.php';

// ----- EXTRA FUNCTIONS

// ----- CONFIG FILES
include_once $REX['INCLUDE_PATH'].'/ctype.inc.php';
include_once $REX['INCLUDE_PATH'].'/clang.inc.php';

// ----- SET CLANG
if (!isset($clang)) $clang = '';
if (!isset($REX['CLANG'][$clang]) or $REX['CLANG'][$clang] == '')
{
  $REX['CUR_CLANG'] = 0;
  $clang = 0;
}else
{
  $REX['CUR_CLANG'] = $clang;
}

// ----- SET CTYPE
if (!isset($ctype)) {
  $ctype = 0; 
} else {
  $ctype = $ctype + 0;
}
if (!isset($REX['CTYPE'][$ctype])) $ctype = 0;

// ----- INCLUDE ADDONS
include_once $REX['INCLUDE_PATH']."/addons.inc.php";

?>