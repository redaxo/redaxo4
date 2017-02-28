<?php

/**
 * Hauptkonfigurationsdatei
 * @package redaxo4
 * @version svn:$Id$
 */

define('REX_MIN_PHP_VERSION', '5.3.0');

if (version_compare(PHP_VERSION, REX_MIN_PHP_VERSION) < 0) {
    // out the error directly instead of trigger_error() to be 100% sure it is displayed and not hidden within any logfile
    echo 'PHP version >=' . REX_MIN_PHP_VERSION . ' needed!';
    exit();
}

// -----------------

if (!$REX['GG']) {
    $REX['GG'] = false;
}

// ----------------- SERVER VARS

// Setupservicestatus - if everything ok -> false; if problem set to true;
$REX['SETUP'] = true;
$REX['SERVER'] = "www.redaxo.org";
$REX['SERVERNAME'] = "REDAXO";
$REX['VERSION'] = "4";
$REX['SUBVERSION'] = "7";
$REX['MINORVERSION'] = "dev";
$REX['ERROR_EMAIL'] = "";
$REX['FILEPERM'] = octdec(664); // oktaler wert
$REX['DIRPERM'] = octdec(775); // oktaler wert
$REX['INSTNAME'] = "rex20130403120000";
$REX['SESSION_DURATION'] = 7200;

// Is set first time SQL Object ist initialised
$REX['MYSQL_VERSION'] = "";

// default article id
$REX['START_ARTICLE_ID'] = 1;

// if there is no article -> change to this article
$REX['NOTFOUND_ARTICLE_ID'] = 1;

// default clang id
$REX['START_CLANG_ID'] = 0;

// default template id, if > 0 used as default, else template_id determined by inheritance
$REX['DEFAULT_TEMPLATE_ID'] = 0;

// default language
$REX['LANG'] = "de_de";

// activate frontend mod_rewrite support for url-rewriting
// Boolean: true/false
$REX['MOD_REWRITE'] = false;

// activate gzip output support
// reduces amount of data need to be send to the client, but increases cpu load of the server
$REX['USE_GZIP'] = "false"; // String: "true"/"false"/"frontend"/"backend"

// activate e-tag support
// tag content with a cache key to improve usage of client cache
$REX['USE_ETAG'] = "false"; // String: "true"/"false"/"frontend"/"backend"

// activate last-modified support
// tag content with a last-modified timestamp to improve usage of client cache
$REX['USE_LAST_MODIFIED'] = "false"; // String: "true"/"false"/"frontend"/"backend"

// activate md5 checksum support
// allow client to validate content integrity
$REX['USE_MD5'] = "false"; // String: "true"/"false"/"frontend"/"backend"

// versch. Pfade
$REX['MEDIA_DIR'] = 'files';
$REX['MEDIA_ADDON_DIR'] = 'files/addons';

$REX['INCLUDE_PATH']   = realpath($REX['HTDOCS_PATH'] . 'redaxo/include');
$REX['GENERATED_PATH'] = realpath($REX['HTDOCS_PATH'] . 'redaxo/include/generated');
$REX['FRONTEND_PATH']  = realpath($REX['HTDOCS_PATH']);
$REX['MEDIAFOLDER']    = realpath($REX['HTDOCS_PATH'] . $REX['MEDIA_DIR']);

// Prefixes
$REX['TABLE_PREFIX']  = 'rex_';
$REX['TEMP_PREFIX']   = 'tmp_';

// Frontenddatei
$REX['FRONTEND_FILE']	= 'index.php';

// Passwortverschlüsselung, z.B: md5 / mcrypt ...
$REX['PSWFUNC'] = "sha1";

// bei fehllogin 5 sekunden kein relogin moeglich
$REX['RELOGINDELAY'] = 5;

// maximal erlaubte login versuche
$REX['MAXLOGINS'] = 50;

// maximal erlaubte anzahl an sprachen
$REX['MAXCLANGS'] = 15;

// Page auf die nach dem Login weitergeleitet wird
$REX['START_PAGE'] = 'structure';

// Zeitzone setzen
$REX['TIMEZONE'] = 'Europe/Berlin';

if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set($REX['TIMEZONE']);
}

error_reporting(error_reporting() & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

// ----------------- OTHER STUFF
$REX['SYSTEM_ADDONS']                   = array('import_export', 'metainfo', 'be_search', 'image_manager', 'install');

// ----------------- MEDIA RELATED
$REX['MEDIAPOOL']['BLOCKED_EXTENSIONS'] = array('.php', '.php3', '.php4', '.php5', '.php6', '.phtml', '.pl', '.asp', '.aspx', '.cfm', '.jsp');
$REX['MEDIAPOOL']['IMAGE_EXTENSIONS']   = array('gif', 'jpeg', 'jpg', 'png', 'bmp');
$REX['MEDIAPOOL']['IMAGE_TYPES']        = array('image/gif', 'image/jpg', 'image/jpeg', 'image/png', 'image/x-png', 'image/pjpeg', 'image/bmp');
$REX['MEDIAPOOL']['ALLOWED_DOCTYPES']   = array('bmp', 'css', 'doc', 'docx', 'eps', 'gif', 'gz', 'jpg', 'mov', 'mp3', 'ogg', 'pdf', 'png', 'ppt', 'pptx', 'pps', 'ppsx', 'rar', 'rtf', 'swf', 'tar', 'tif', 'txt', 'wma', 'xls', 'xlsx', 'zip');

// ----------------- DB1
$REX['DB']['1']['HOST'] = "localhost";
$REX['DB']['1']['LOGIN'] = "root";
$REX['DB']['1']['PSW'] = "";
$REX['DB']['1']['NAME'] = 'redaxo_4_7';
$REX['DB']['1']['PERSISTENT'] = false;

// ----------------- DB2 - if necessary
$REX['DB']['2']['HOST'] = "";
$REX['DB']['2']['LOGIN'] = "";
$REX['DB']['2']['PSW'] = "";
$REX['DB']['2']['NAME'] = "";
$REX['DB']['2']['PERSISTENT'] = false;

// ----------------- Accesskeys
$REX['ACKEY']['SAVE']   = 's';
$REX['ACKEY']['APPLY']  = 'x';
$REX['ACKEY']['DELETE'] = 'd';
$REX['ACKEY']['ADD']    = 'a';
// Wenn 2 Add Aktionen auf einer Seite sind (z.b. Struktur)
$REX['ACKEY']['ADD_2']  = 'y';
$REX['ACKEY']['LOGOUT'] = 'l';

// ------ Accesskeys for Addons
// $REX['ACKEY']['ADDON']['metainfo'] = 'm';

// ----------------- REX PERMS

// ----- allgemein
$REX['PERM'] = array();

// ----- optionen
$REX['EXTPERM'] = array();
$REX['EXTPERM'][] = 'advancedMode[]';
$REX['EXTPERM'][] = 'accesskeys[]';
$REX['EXTPERM'][] = 'moveSlice[]';
$REX['EXTPERM'][] = 'moveArticle[]';
$REX['EXTPERM'][] = 'moveCategory[]';
$REX['EXTPERM'][] = 'copyArticle[]';
$REX['EXTPERM'][] = 'copyContent[]';
$REX['EXTPERM'][] = 'publishArticle[]';
$REX['EXTPERM'][] = 'publishCategory[]';
$REX['EXTPERM'][] = 'article2startpage[]';
$REX['EXTPERM'][] = 'article2category[]';
$REX['EXTPERM'][] = 'editMediaCategories[]';

// ----- extras
$REX['EXTRAPERM'] = array();
$REX['EXTRAPERM'][] = 'editContentOnly[]';

// ----- standard variables
$REX['VARIABLES'] = array();
$REX['VARIABLES'][] = 'rex_var_globals';
$REX['VARIABLES'][] = 'rex_var_config';
$REX['VARIABLES'][] = 'rex_var_article';
$REX['VARIABLES'][] = 'rex_var_category';
$REX['VARIABLES'][] = 'rex_var_template';
$REX['VARIABLES'][] = 'rex_var_value';
$REX['VARIABLES'][] = 'rex_var_link';
$REX['VARIABLES'][] = 'rex_var_media';

// deactivate session cache limiter
session_cache_limiter(false);

// ----------------- default values
if (!isset($REX['NOFUNCTIONS'])) {
    $REX['NOFUNCTIONS'] = false;
}

// ----------------- INCLUDE FUNCTIONS
if (!$REX['NOFUNCTIONS']) {
    include_once $REX['INCLUDE_PATH'] . '/functions.inc.php';
}

// ----- SET CLANG
include_once $REX['INCLUDE_PATH'] . '/clang.inc.php';

$REX['CUR_CLANG']  = rex_request('clang', 'rex-clang-id', $REX['START_CLANG_ID']);

if (rex_request('article_id', 'int') == 0) {
    $REX['ARTICLE_ID'] = $REX['START_ARTICLE_ID'];
} else {
    $REX['ARTICLE_ID'] = rex_request('article_id', 'rex-article-id', $REX['NOTFOUND_ARTICLE_ID']);
}
