<?php

// -----------------

if (!$REX[GG]) $REX[GG] = false;

// ----------------- SERVER VARS

$REX[SETUP] = true; 			// Setupservicestatus - if everything ok -> false; if problem set to true;
$REX[SERVER] = "redaxo.com";
$REX[SERVERNAME] = "Redaxo-Demo";
$REX[error_emailaddress] = "jan.kristinus@pergopa.de";
$REX[version] = "2.7";
$REX[subversion] = "4";
$REX[STARTARTIKEL_ID] = 1; // FIRTS ARTICLE
$REX[STATS] = 1; // STATS
$REX[LANG] = de_DE; // select default language
$REX[MOD_REWRITE] = false; // activate mod_rewrite support
$REX[WWW_PATH] = ""; //
$REX[DOC_ROOT] = ""; //
$REX[INCLUDE_PATH] = $REX[DOC_ROOT].$REX[HTDOCS_PATH]."redaxo/include"; //
$REX[MEDIAFOLDER] = $REX[HTDOCS_PATH]."files"; //
$REX[BCONTENT] = false; //
$REX[COMMUNITY] = false;
$REX[CACHING] = false; // Caching
$REX[CACHING_DEBUG] = false; // Shows debugmessage
$REX[TABLE_PREFIX] = "rex_";
$REX[ONOFFCHECK] = false;

// ----------------- DB1
$DB[1][HOST] = "localhost";
$DB[1][LOGIN] = "username";
$DB[1][PSW] = "password";
$DB[1][NAME] = "databasename";

// ----------------- DB2 - if necessary
$DB[2][HOST] = "";
$DB[2][LOGIN] = "";
$DB[2][PSW] = "";
$DB[2][NAME] = "";

// ----------------- INCLUDE FUNCTIONS
if(!$REX[NOFUNCTIONS]) include_once ($REX[INCLUDE_PATH].'/functions.inc.php');

// -----------------
if (!isset($category_id) or $category_id == "") $category_id = 0;

// ----------------- set to default
$REX[NOFUNCTIONS] = false;

?>
