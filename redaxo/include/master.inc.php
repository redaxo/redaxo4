<?

// ------------------------------------------------------------

if (!$REX[GG]) $REX[GG] = false;

// ------------------------------------------------------------ globals

// ----------------- SERVER VARS

$REX[SERVER] = "redaxo.com";
$REX[SERVERNAME] = "Redaxo-Demo";
$REX[error_emailaddress] = "";
$REX[version] = "2.7";
$REX[STARTARTIKEL_ID] = 1;
$REX[STATS] = 1;

// select default language
$REX[LANG] = en_GB;

// activate mod_rewrite support
$REX[MOD_REWRITE] = false;

// Windows:
// Pfade absolut eintragen
// Linux:
// nicht ändern

$REX[WWW_PATH] = ""; //
$REX[DOC_ROOT] = "";
$REX[INCLUDE_PATH] = $REX[DOC_ROOT].$REX[HTDOCS_PATH]."redaxo/include";
$REX[MEDIAFOLDER] = $REX[HTDOCS_PATH]."files";

//

$REX[BARRIEREFREI] = false;
$REX[COMMUNITY] = false;

// Advanced Caching
$REX[CACHING] = false;
$REX[CACHING_DEBUG] = false;

// ----------------- DATENBANK

$DB[1][HOST] = "localhost";
$DB[1][LOGIN] = "redaxo";
$DB[1][PSW] = "redaxo";
$DB[1][NAME] = "redaxo";

$DB[2][HOST] = "";
$DB[2][LOGIN] = "";
$DB[2][PSW] = "";
$DB[2][NAME] = "";


// ----------------- INCLUDE FUNCTIONS
if(!$REX[NOFUNCTIONS]){
	include($REX[INCLUDE_PATH].'/functions.inc.php');
}


if (!isset($category_id) or $category_id == "") $category_id = 0;

// set to default
$REX[NOFUNCTIONS] = false;

?>
