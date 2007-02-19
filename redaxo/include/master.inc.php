<?php

/** 
 * 
 * @package redaxo3 
 * @version $Id$ 
 */

// -----------------

if (!$REX) $REX = array();
if (!$REX['GG']) $REX['GG'] = false;
if (!isset($page)) $page = '';

// ----------------- SERVER VARS

$REX['SETUP'] = false; 			// Setupservicestatus - if everything ok -> false; if problem set to true;
$REX['SERVER'] = "redaxo.de";
$REX['SERVERNAME'] = "REDAXO";
$REX['ERROR_EMAIL'] = "jan.kristinus@pergopa.de";
$REX['VERSION'] = "3";
$REX['SUBVERSION'] = "3";
$REX['MYSQL_VERSION'] = ""; // Is set first time SQL Object ist initialised
$REX['START_ARTICLE_ID'] = 1; // FIRST ARTICLE
$REX['NOTFOUND_ARTICLE_ID'] = 1; // if there is no article -> change to this article
$REX['LANG'] = "de_de"; // select default language
$REX['MOD_REWRITE'] = false; // activate mod_rewrite support
$REX['INCLUDE_PATH'] = $REX['HTDOCS_PATH']."redaxo/include"; // 
$REX['MEDIAFOLDER'] = $REX['HTDOCS_PATH']."files"; //
$REX['TABLE_PREFIX'] = "rex_";
$REX['TEMP_PREFIX'] = "tmp_";
$REX['FILEPERM'] = octdec(775); // oktaler wert
$REX['INSTNAME'] = "rex20060101010101";
$REX['PSWFUNC'] = ""; // wenn erwünscht: md5 / mcrypt ...
$REX['RELOGINDELAY'] = 5; // bei fehllogin 5 sekunden kein relogin moeglich
$REX['MAXLOGINS'] = 50; // maximal erlaubte versuche
$REX['START_PAGE'] = 'structure'; // Page auf die nach dem Login weitergeleitet wird

// ----------------- OTHER VARS
$REX["MEDIAPOOL"]["BLOCKED_EXTENSIONS"] = array(".php",".php3",".php4",".php5",".phtml",".pl",".asp",".aspx",".cfm");

// ----------------- DB1
$REX['DB']['1']['HOST'] = "localhost";
$REX['DB']['1']['LOGIN'] = "root";
$REX['DB']['1']['PSW'] = "";
$REX['DB']['1']['NAME'] = "redaxo3_3";

// ----------------- DB2 - if necessary
$REX['DB']['2']['HOST'] = "";
$REX['DB']['2']['LOGIN'] = "";
$REX['DB']['2']['PSW'] = "";
$REX['DB']['2']['NAME'] = "";

// ----------------- default values
if (!isset($article_id) or $article_id == '') $article_id = $REX['START_ARTICLE_ID'];
if (!isset($category_id) or $category_id == '') $category_id = '0';
if (!isset($ctype) or $ctype == '') $ctype = '0';
if (!isset($clang) or $clang =='') $clang = '0';
if (!isset($REX['NOFUNCTIONS'])) $REX['NOFUNCTIONS'] = false;

// ----------------- INCLUDE FUNCTIONS
if(!$REX['NOFUNCTIONS']) include_once ($REX['INCLUDE_PATH'].'/functions.inc.php');

?>