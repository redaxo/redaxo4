<?

// ------------------------------------------------------------

if (!$REX[GG]) $REX[GG] = false;

// ------------------------------------------------------------ globals

// ----------------- SERVER VARS

$REX[SERVER] = "redaxo.de";
$REX[SERVERNAME] = "Redaxo";
$REX[error_emailaddress] = "";
$REX[version] = "2.6";
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
$REX[CACHING] = true;
$REX[CACHING_DEBUG] = true;

// ----------------- DATENBANK

$DB[1][HOST] = "localhost";
$DB[1][LOGIN] = "vscopabr";
$DB[1][PSW] = "hu34zi7";
$DB[1][NAME] = "usrdb_vscopabr_redaxonew";

$DB[2][HOST] = "";
$DB[2][LOGIN] = "";
$DB[2][PSW] = "";
$DB[2][NAME] = "";


// ----------------- INCLUDE FUNCTIONS
if(!$REX[NOFUNCTIONS]){
	include($REX[INCLUDE_PATH].'/functions.inc.php');
}


if (!isset($category_id) or $category_id == "") $category_id = 0;


?>
