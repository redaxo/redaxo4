<?php

error_reporting(E_ALL ^ E_DEPRECATED);

// TODO be_style + agk Skin defaul installed
// TODO addons.inc, plugins.inc entsprechend
// TODO nur sprache XY ins release

// php5 noetig, wg simple_xml
if (version_compare(phpversion(), $needed = '5.0.0', '<') == 1) {
    echo 'Requires PHP >= ' . $needed;
    exit();
}

$name = null;
$version = null;
if (isset($argv) && count($argv) > 1) {
    if (!empty($argv[1])) {
        $version = $argv[1];
    }
    if (!empty($argv[2])) {
        $name = $argv[2];
    }

    // Start Build-Script
    buildRelease($name, $version);
} else {
    echo '
/**
 * Erstellt ein REDAXO Release.
 *
 *
 * Verwendung in der Console:
 *
 *  Erstelles eines Release mit Versionsnummer:
 *  "php release.php 4.2"
 *
 *
 * Vorgehensweise des release-scripts:
 *  - Ordnerstruktur kopieren nach release/redaxo_<Datum>
 *  - Dateien kopieren
 *  - Sprachdateien zu UTF-8 konvertieren
 *  - CVS Ordner loeschen
 *  - master.inc.php anpassen
 *  - functions.inc.php die compat klasse wird einkommentiert
 */
';
}



function buildRelease($name = null, $version = null)
{
    // Ordner in dem das release gespeichert wird
    // ohne "/" am Ende!
    $cfg_path = 'release';

    // Dateien/Verzeichnisse die in keinem Ordner kopiert werden sollen
    $systemFiles = array(
        '.cache',
        '.settings',
        '.svn',
        '.project',
        '.buildpath',
        '.idea',
        '.DS_Store',
        '.git'
    );
    // Dateien/Verzeichnisse die nur in bestimmten Ordnern nicht kopiert werden sollen
    $ignoreFiles = array(
        './.gitmodules',
        './.gitignore',
        './.htaccess',
        './release.php',
        './release.xml',
        './_db_schema.png',
        './_db_schema.mwb',
        './test',
        './bin',
        './redaxo/include/data',
        './redaxo/include/generated',
        './redaxo/include/addons',
        './' . $cfg_path,
        './coding_standards.php',
        './lang_scan.php',
        './files',
    );
    // Addons die vorinstalliert sein sollen
    $preinstallAddons = array(
      'be_style',
    );
    // Plugins die vorinstalliert sein sollen
    $preinstallPlugins = array(
        'be_style' => 'agk_skin',
    );

    // USE ONLY THESE LANGS
    $use_lang = array(
        'de_de',
        'en_gb',
    );



    // MAIN
    //////////////////////////////////////////////////////////////////////////////
    if (!$name) {
        $name = 'redaxo';
        if (!$version) {
            $name .= date('ymd');
        } else {
            $name .= str_replace('.', '_', $version);
        }
    }

    if ($version) {
        $version = explode('.', $version);
    }

    $releaseConfigs = getReleaseConfigs();
    $systemAddons = getSystemAddons();
    $systemName = $name;
    foreach ($releaseConfigs as $releaseConfig) {
        $path = $cfg_path;
        $name = $systemName . '_' . $releaseConfig['name'];
        if ($releaseConfig['name'] == 'default') {
            $name = $systemName;
        }

        if (substr($path, -1) != '/') {
            $path .= '/';
        }

        if (!is_dir($path)) {
            mkdir($path);
        }

        $dest = $path . $name;

        if (is_dir($dest)) {
            trigger_error('release folder already exists!', E_USER_ERROR);
        } else {
            mkdir($dest);
        }

        echo '>>> BUILD REDAXO release ' . $name . '..' . PHP_EOL;
        echo PHP_EOL . '> read files' . PHP_EOL;

        // Ordner und Dateien auslesen
        echo PHP_EOL . '> copy files' . PHP_EOL;
        $structure = readFolderStructure('.',
            array_merge(
                $systemFiles,
                $ignoreFiles
            )
        );
        copyFolderStructure($structure, $dest, $use_lang);

        echo PHP_EOL . '> copy addons' . PHP_EOL;
        foreach (array_merge($releaseConfig['addons'], $systemAddons) as $addon) {
            echo PHP_EOL . '>> ' . $addon . PHP_EOL;
            $structure = readFolderStructure(
                './redaxo/include/addons/' . $addon,
                $systemFiles
            );
            copyFolderStructure($structure, $dest, $use_lang);
        }

        // Ordner die wir nicht mitkopiert haben anlegen
        // Der generated Ordner enthält sehr viele Daten,
        // das kopieren würde sehr lange dauern und ist unnötig
        $manual_dirs = array(
            $dest . '/files',
            $dest . '/redaxo/include/data',
            $dest . '/redaxo/include/generated',
            $dest . '/redaxo/include/generated/articles',
            $dest . '/redaxo/include/generated/templates',
            $dest . '/redaxo/include/generated/files',
            $dest . '/files/addons',
            $dest . '/files/addons/be_style',
            $dest . '/files/addons/be_style/plugins',
            $dest . '/files/addons/be_style/plugins/agk_skin',
        );
        foreach ($manual_dirs as $dir) {
            if (!file_exists($dir)) {
                mkdir($dir);
            }
        }

        // Ordner die eine .redaxo Datei bekommen und
        // somit dafür sorgen, dass dieser Ordner mit jedem
        // Entpacker verwendet wird
        $manual_dirs = array(
            $dest . '/files',
            $dest . '/redaxo/include/data',
            $dest . '/redaxo/include/generated/articles',
            $dest . '/redaxo/include/generated/templates',
            $dest . '/redaxo/include/generated/files',
        );
        foreach ($manual_dirs as $dir) {
            file_put_contents( $dir . '/.redaxo' , '// Ordner für abgelegte Dateien von redaxo' );
        }

        echo PHP_EOL . '> fix master.inc.php' . PHP_EOL;

        // master.inc.php anpassen
        $master = $dest . '/redaxo/include/master.inc.php';
        $h = fopen($master, 'r');
        $cont = fread($h, filesize($master));
        fclose($h);

        $cont = ereg_replace("(REX\['SETUP'\].?\=.?)[^;]*", '\\1true', $cont);
        $cont = ereg_replace("(REX\['SERVER'\].?\=.?)[^;]*", '\\1"www.redaxo.org"', $cont);
        $cont = ereg_replace("(REX\['SERVERNAME'\].?\=.?)[^;]*", '\\1"REDAXO"', $cont);
        $cont = ereg_replace("(REX\['ERROR_EMAIL'\].?\=.?)[^;]*", '\\1""', $cont);
        $cont = ereg_replace("(REX\['INSTNAME'\].?\=.?\")[^\"]*", "\\1" . 'rex' . date('Ymd') . '000000', $cont);
        $cont = ereg_replace("(REX\['LANG'\].?\=.?)[^;]*", '\\1"de_de"', $cont);
        $cont = ereg_replace("(REX\['START_ARTICLE_ID'\].?\=.?)[^;]*", '\\11', $cont);
        $cont = ereg_replace("(REX\['NOTFOUND_ARTICLE_ID'\].?\=.?)[^;]*", '\\11', $cont);
        $cont = ereg_replace("(REX\['MOD_REWRITE'\].?\=.?)[^;]*", '\\1false', $cont);
        $cont = ereg_replace("(REX\['DEFAULT_TEMPLATE_ID'\].?\=.?)[^;]*", '\\10', $cont);

        $cont = ereg_replace("(REX\['DB'\]\['1'\]\['HOST'\].?\=.?)[^;]*", '\\1"localhost"', $cont);
        $cont = ereg_replace("(REX\['DB'\]\['1'\]\['LOGIN'\].?\=.?)[^;]*", '\\1"root"', $cont);
        $cont = ereg_replace("(REX\['DB'\]\['1'\]\['PSW'\].?\=.?)[^;]*", '\\1""', $cont);

        if ($version) {
            if (empty($version[1])) {
                $version[1] = '0';
            }

            if (empty($version[2])) {
                $version[2] = '0';
            }

            $cont = ereg_replace("(REX\['DB'\]\['1'\]\['NAME'\].?\=.?)[^;]*", '\\1"redaxo_' . implode('_', $version) . '"', $cont);
            $cont = ereg_replace("(REX\['VERSION'\].?\=.?)[^;]*"     , '\\1"' . $version[0] . '"', $cont);
            $cont = ereg_replace("(REX\['SUBVERSION'\].?\=.?)[^;]*"  , '\\1"' . $version[1] . '"', $cont);
            $cont = ereg_replace("(REX\['MINORVERSION'\].?\=.?)[^;]*", '\\1"' . $version[2] . '"', $cont);
        } else {
            $cont = ereg_replace("(REX\['DB'\]\['1'\]\['NAME'\].?\=.?)[^;]*", '\\1"redaxo"', $cont);
        }

        $h = fopen($master, 'w+');
        if (fwrite($h, $cont, strlen($cont)) > 0) {
            fclose($h);
        }

        echo PHP_EOL . '> fix functions.inc.php' . PHP_EOL;

        // functions.inc.php anpassen
        $functions = $dest . '/redaxo/include/functions.inc.php';
        $h = fopen($functions, 'r');
        $cont = fread($h, filesize($functions));
        fclose($h);

        echo PHP_EOL . '>> activate compatibility API' . PHP_EOL;

        // compat klasse aktivieren
        $cont = str_replace(
            "// include_once \$REX['INCLUDE_PATH'].'/classes/class.compat.inc.php';",
            "include_once \$REX['INCLUDE_PATH'].'/classes/class.compat.inc.php';",
            $cont,
            $count
        );

        if ($count != 1) {
            trigger_error('Error while activating compat class!', E_USER_ERROR);
            exit();
        }

        $h = fopen($functions, 'w+');
        if (fwrite($h, $cont, strlen($cont)) > 0) {
            fclose($h);
        }


        // addons.inc.php anpassen / Addons vorinstallieren
        ////////////////////////////////////////////////////////////////////////////
        echo PHP_EOL . '> fix addons.inc.php' . PHP_EOL;

        $addons = $dest . '/redaxo/include/addons.inc.php';
        $h = fopen($addons, 'r');
        $cont = fread($h, filesize($addons));
        fclose($h);

        $preinstall = PHP_EOL;
        foreach ($preinstallAddons as $addon) {
            $preinstall .= '
    $REX[\'ADDON\'][\'install\'][\'' . $addon . '\'] = \'1\';
    $REX[\'ADDON\'][\'status\'][\'' . $addon . '\']  = \'1\';
            ' . PHP_EOL;

            // addon files kopieren
            echo PHP_EOL . '>> Copy ' . $addon . ' files..' . PHP_EOL;
            copy_r(
                dirname(__FILE__) . '/redaxo/include/addons/' . $addon . '/files',
                dirname(__FILE__) . '/' . $dest . '/files/addons/' . $addon
                );
        }

        $cont = ereg_replace("(\/\/.---.DYN.*\/\/.---.\/DYN)", '// --- DYN' . $preinstall . '// --- /DYN', $cont);

        $h = fopen($addons, 'w+');
        if (fwrite($h, $cont, strlen($cont)) > 0) {
            fclose($h);
        }


        // plugins.inc.php anpassen / Plugins vorinstallieren
        ////////////////////////////////////////////////////////////////////////////
        echo PHP_EOL . '> fix plugins.inc.php' . PHP_EOL;

        $plugins = $dest . '/redaxo/include/plugins.inc.php';
        $h = fopen($plugins, 'r');
        $cont = fread($h, filesize($plugins));
        fclose($h);

        $preinstall = PHP_EOL;
        foreach ($preinstallPlugins as $addon => $plugin) {
            $preinstall .= '
    $REX[\'ADDON\'][\'plugins\'][\'' . $addon . '\'][\'install\'][\'' . $plugin . '\'] = \'1\';
    $REX[\'ADDON\'][\'plugins\'][\'' . $addon . '\'][\'status\'][\'' . $plugin . '\']  = \'1\';
            ' . PHP_EOL;

            // plugin files kopieren
            echo PHP_EOL . '>> Copy ' . $plugin . ' files..' . PHP_EOL;
            copy_r(
                dirname(__FILE__) . '/redaxo/include/addons/' . $addon . '/plugins/' . $plugin . '/files',
                dirname(__FILE__) . '/' . $dest . '/files/addons/' . $addon . '/plugins/' . $plugin
                );
        }

        $cont = ereg_replace("(\/\/.---.DYN.*\/\/.---.\/DYN)", '// --- DYN' . $preinstall . '// --- /DYN', $cont);

        $h = fopen($plugins, 'w+');
        if (fwrite($h, $cont, strlen($cont)) > 0) {
            fclose($h);
        }


        echo PHP_EOL . '>>> BUILD "' . $name . '" Finished' . "\n\n";
    }

    echo "\nDEST: $dest";
    echo "\nName: $name";

    $phar = new PharData('release/' . $name . '.zip');
    $phar->buildFromDirectory(dirname(__FILE__) . '/' . $dest);
    $phar->compressFiles(Phar::GZ);

    echo PHP_EOL . '> FINISHED' . PHP_EOL;
}

/**
 * Returns the content of the given folder
 *
 * @param $dir Path to the folder
 * @return Array Content of the folder or false on error
 * @author Markus Staab <markus.staab@redaxo.de>
 */
function readFolder($dir)
{
    if (!is_dir($dir)) {
        trigger_error('Folder "' . $dir . '" is not available or not a directory');
        return false;
    }
    $hdl = opendir($dir);
    $folder = array ();
    while (false !== ($file = readdir($hdl))) {
        $folder[] = $file;
    }

    return $folder;
}

/**
 * Returns the content of the given folder.
 * The content will be filtered with the given $fileprefix
 *
 * @param $dir Path to the folder
 * @param $fileprefix Fileprefix to filter
 * @return Array Filtered-content of the folder or false on error
 * @author Markus Staab <markus.staab@redaxo.de>
 */

function readFilteredFolder($dir, $fileprefix)
{
    $filtered = array ();
    $folder = readFolder($dir);

    if (!$folder) {
        return false;
    }

    foreach ($folder as $file) {
        if (endsWith($file, $fileprefix)) {
            $filtered[] = $file;
        }
    }

    return $filtered;
}

/**
 * Returns the files of the given folder
 *
 * @param $dir Path to the folder
 * @return Array Files of the folder or false on error
 * @author Markus Staab <markus.staab@redaxo.de>
 */
function readFolderFiles($dir, $except = array ())
{
    $folder = readFolder($dir);
    $files = array ();

    if (!$folder) {
        return false;
    }

    foreach ($folder as $file) {
        if (is_file($dir . '/' . $file) && !inExcept($dir, $file, $except)) {
            $files[] = $file;
        }
    }

    return $files;
}

function inExcept($dir, $file, $excepts = array())
{
    foreach ($excepts as $except) {
        if (strpos($except, '/') === false) {
            // handle relative file except
            if ($file == $except) {
                return true;
            }
        } else {
            // handle absolute except
            if (($dir . '/' . $file) == $except) {
                return true;
            }
        }
    }
    return false;
}

/**
 * Returns the subfolders of the given folder
 *
 * @param $dir Path to the folder
 * @param $ignore_dots True if the system-folders "." and ".." should be ignored
 * @return Array Subfolders of the folder or false on error
 * @author Markus Staab <markus.staab@redaxo.de>
 */
function readSubFolders($dir, $ignore_dots = true)
{
    $folder = readFolder($dir);
    $folders = array ();

    if (!$folder) {
        return false;
    }

    foreach ($folder as $file) {
        if ($ignore_dots && ($file == '.' || $file == '..')) {
            continue;
        }
        if (is_dir($dir . '/' . $file)) {
            $folders[] = $file;
        }
    }

    return $folders;
}

function readFolderStructure($dir, $except = array ())
{
    $result = array ();

    _readFolderStructure($dir, $except, $result);

    uksort($result, 'sortFolderStructure');

    return $result;
}

function _readFolderStructure($dir, $except, & $result)
{
    $files = readFolderFiles($dir, $except);
    $subdirs = readSubFolders($dir);

    if (is_array($subdirs)) {
        foreach ($subdirs as $key => $subdir) {
            if (inExcept($dir, $subdir, $except)) {
                unset($subdirs[$key]);
                continue;
            }

            _readFolderStructure($dir . '/' . $subdir, $except, $result);
        }
    }

    $result[$dir] = array_merge($files, $subdirs);

    return $result;
}

function sortFolderStructure($path1, $path2)
{
    return strlen($path1) > strlen($path2) ? 1 : -1;
}

function copyFolderStructure($structure, $dest, $use_lang)
{
    // Ordner/Dateien kopieren
    foreach ($structure as $path => $content) {
        // Zielordnerstruktur anlegen
        $temp_path = '';
        foreach (explode('/', $dest . '/' . $path) as $pathdir) {
            if (!is_dir($temp_path . $pathdir . '/')) {
                mkdir($temp_path . $pathdir . '/');
            }
            $temp_path .= $pathdir . '/';
        }

        // Dateien kopieren/Ordner anlegen
        foreach ($content as $dir) {
            if (is_file($path . '/' . $dir)) {
                if (substr($dir, -5) == '.lang' && !in_array(substr($dir, 0, 5), $use_lang)) {
                 echo '> skipping lang file ' . $dir . PHP_EOL;
                } else {
                    copy($path . '/' . $dir, $dest . '/' . $path . '/' . $dir);
                }
            } elseif (is_dir($path . '/' . $dir)) {
                if (!file_exists($dest . '/' . $path . '/' . $dir)) {
                    mkdir($dest . '/' . $path . '/' . $dir);
                }
            }
        }
    }
}

function getReleaseConfigs()
{
    $config_file = 'release.xml';
    if (!file_exists($config_file)) {
        trigger_error('Required config-file not found "' . $config_file . '"', E_USER_ERROR);
        exit();
    }

    $configs = simplexml_load_file($config_file);
    $releases = array();
    foreach ($configs as $config) {
        $release = array();
        $release['name'] = xmlAttribute($config, 'name');
        $release['addons'] = array();

        if ($config->addons) {
            foreach ($config->addons[0] as $addon) {
                $release['addons'][] = xmlAttribute($addon, 'name');
            }
        }
        $releases[] = $release;
    }
    return $releases;
}

function xmlAttribute($xmlElement, $attrName, $default = null)
{
        $attrs = $xmlElement->attributes();
        return isset($attrs[$attrName]) ? (string) $attrs[$attrName] : $default;
}

function getSystemAddons()
{
    $master = 'redaxo/include/master.inc.php';
    if (!file_exists($master)) {
        trigger_error('config "' . $master . '" not found!', E_USER_ERROR);
        exit();
    }

    // Warnungen vermeiden
    $REX = array();
    $REX['GG'] = false;
    $REX['REDAXO'] = true;
    $REX['HTDOCS_PATH'] = './';

    require $master;
    return $REX['SYSTEM_ADDONS'];
}

function copy_r( $path, $dest )
        {
                if ( is_dir($path) ) {
                        @mkdir( $dest );
                        $objects = scandir($path);
                        if ( sizeof($objects) > 0 ) {
                                foreach ( $objects as $file ) {
                                        if ( $file == '.' || $file == '..' ) {
                                                continue;
                                        }
                                        // go on
                                        if ( is_dir( $path . DIRECTORY_SEPARATOR . $file ) ) {
                                                copy_r( $path . DIRECTORY_SEPARATOR . $file, $dest . DIRECTORY_SEPARATOR . $file );
                                        } else {
                                                copy( $path . DIRECTORY_SEPARATOR . $file, $dest . DIRECTORY_SEPARATOR . $file );
                                        }
                                }
                        }
                        return true;
                } elseif ( is_file($path) ) {
                        return copy($path, $dest);
                } else {
                        return false;
                }
        }
