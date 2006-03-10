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
$REX['error_emailaddress'] = "jan.kristinus@pergopa.de";
$REX['version'] = "3";
$REX['subversion'] = "2";
$REX['MYSQL_VERSION'] = ""; // Is set first time SQL Object ist initialised
$REX['STARTARTIKEL_ID'] = 1; // FIRST ARTICLE
$REX['LANG'] = "de_de"; // select default language
$REX['MOD_REWRITE'] = false; // activate mod_rewrite support
$REX['INCLUDE_PATH'] = $REX['HTDOCS_PATH']."redaxo/include"; // 
$REX['MEDIAFOLDER'] = $REX['HTDOCS_PATH']."files"; //
$REX['TABLE_PREFIX'] = "rex_";
$REX['FILEPERM'] = octdec(775); // oktaler wert
$REX['INSTNAME'] = "rex20060101010101";
$REX['PSWFUNC'] = ""; // wenn erwünscht: md5 / mcrypt ...
$REX['MAXLOGINS'] = 20; // maximale loginversuche

// ----------------- DB1
$REX['DB']['1']['HOST'] = "localhost";
$REX['DB']['1']['LOGIN'] = "root";
$REX['DB']['1']['PSW'] = "";
$REX['DB']['1']['NAME'] = "redaxo3_2";

// ----------------- DB2 - if necessary
$REX['DB']['2']['HOST'] = "";
$REX['DB']['2']['LOGIN'] = "";
$REX['DB']['2']['PSW'] = "";
$REX['DB']['2']['NAME'] = "";

// ----------------- INCLUDE FUNCTIONS
if(!isset($REX['NOFUNCTIONS']) or !$REX['NOFUNCTIONS']) include_once ($REX['INCLUDE_PATH'].'/functions.inc.php');

// -----------------
if (!isset($category_id) or $category_id == "") $category_id = 0;
if (!isset($ctype) or $ctype == "") $ctype = 0;

// ----------------- set to default
$REX['NOFUNCTIONS'] = false;

?>