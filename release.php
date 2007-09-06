<?php

// TODO
// - files Ordner nicht kopieren
// - js Ordner nicht kopieren?
// - media Ordner nicht kopieren?

$name = null;
$version = null;
if(isset($argv) && count($argv) > 1)
{
	if(!empty($argv[1]))
	{
		$version = $argv[1];
	}
	if(!empty($argv[2]))
	{
		$name = $argv[2];
	}

  // Start Build-Script
  buildRelease($name, $version);
}
else
{
  echo '
/**
 * Erstellt ein REDAXO Release.
 *
 *
 * Verwendung in der Console:
 *
 *  Erstelles eines Release mit Versionsnummer:
 *  "php -f release.php 3.3"
 *
 *
 * Vorgehensweise des release-scripts:
 *  - Ordnerstruktur kopieren nach release/redaxo_<Datum>
 *  - Dateien kopieren
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
  $path = $cfg_path;

  if (!$name)
  {
    $name = 'redaxo';
    if(!$version)
      $name .= date('ymd');
    else
      $name .= str_replace('.', '_', $version);
  }

  if($version)
    $version = explode('.', $version);

  if(substr($path, -1) != '/')
    $path .= '/';

  if (!is_dir($path))
    mkdir($path);

  $dest = $path . $name;

  if (is_dir($dest))
    trigger_error('release folder already exists!', E_USER_ERROR);
  else
    mkdir($dest);

  echo '>>> Build REDAXO release..'."\n";
  echo '> read files'."\n";

  // Ordner und Dateien auslesen
  $structure = readFolderStructure('.', array('.project', 'CVS', 'generated', $cfg_path));

  echo '> copy files'."\n";
  // Ordner/Dateien kopieren
  foreach($structure as $path => $content)
  {
    // Zielordnerstruktur anlegen
    $temp_path = '';
    foreach(explode('/', $dest .'/'. $path) as $pathdir)
    {
      if(!is_dir($temp_path . $pathdir .'/'))
      {
        mkdir($temp_path . $pathdir .'/');
      }
      $temp_path .= $pathdir .'/';
    }

    // Dateien kopieren/Ordner anlegen
    foreach($content as $dir)
    {
      if(is_file($path.'/'.$dir))
        copy($path.'/'.$dir, $dest .'/'. $path.'/'.$dir);
      elseif(is_dir($path.'/'.$dir))
        mkdir($dest .'/'. $path.'/'.$dir);
    }
  }

  echo '> delete generated folder content'."\n";

  // Ordner die wir nicht mitkopiert haben anlegen
  // Der generated Ordner enthält sehr viele Daten,
  // das kopieren würde sehr lange dauern und ist unnötig
  mkdir($dest .'/redaxo/include/generated');
  mkdir($dest .'/redaxo/include/generated/articles');
  mkdir($dest .'/redaxo/include/generated/templates');
  mkdir($dest .'/redaxo/include/generated/files');

  echo '> fix master.inc.php'."\n";

  // master.inc.php anpassen
  $master = $dest.'/redaxo/include/master.inc.php';
  $h = fopen($master, 'r');
  $cont = fread($h, filesize($master));
  fclose($h);

  $cont = ereg_replace("(REX\['SETUP'\].?\=.?)[^;]*", '\\1true', $cont);
  $cont = ereg_replace("(REX\['SERVER'\].?\=.?)[^;]*", '\\1"redaxo.de"', $cont);
  $cont = ereg_replace("(REX\['SERVERNAME'\].?\=.?)[^;]*", '\\1"REDAXO"', $cont);
  $cont = ereg_replace("(REX\['ERROR_EMAIL'\].?\=.?)[^;]*", '\\1"jan.kristinus@pergopa.de"', $cont);
  $cont = ereg_replace("(REX\['LANG'\].?\=.?)[^;]*", '\\1"de_de"', $cont);
  $cont = ereg_replace("(REX\['START_ARTICLE_ID'\].?\=.?)[^;]*", '\\11', $cont);
  $cont = ereg_replace("(REX\['NOTFOUND_ARTICLE_ID'\].?\=.?)[^;]*", '\\11', $cont);
  $cont = ereg_replace("(REX\['MOD_REWRITE'\].?\=.?)[^;]*", '\\1false', $cont);

  $cont = ereg_replace("(REX\['DB'\]\['1'\]\['HOST'\].?\=.?)[^;]*", '\\1"localhost"', $cont);
  $cont = ereg_replace("(REX\['DB'\]\['1'\]\['LOGIN'\].?\=.?)[^;]*", '\\1"root"', $cont);
  $cont = ereg_replace("(REX\['DB'\]\['1'\]\['PSW'\].?\=.?)[^;]*", '\\1""', $cont);

  if($version)
  {
    $cont = ereg_replace("(REX\['DB'\]\['1'\]\['NAME'\].?\=.?)[^;]*", '\\1"redaxo_'. implode('_', $version) .'"', $cont);
    $cont = ereg_replace("(REX\['VERSION'\].?\=.?)[^;]*", '\\1'. $version[0], $cont);
    $cont = ereg_replace("(REX\['SUBVERSION'\].?\=.?)[^;]*", '\\1'. $version[1], $cont);
  }
  else
  {
    $cont = ereg_replace("(REX\['DB'\]\['1'\]\['NAME'\].?\=.?)[^;]*", '\\1"redaxo"', $cont);
  }

  $h = fopen($master, 'w+');
  if (fwrite($h, $cont, strlen($cont)) > 0)
    fclose($h);

  echo '> fix functions.inc.php'."\n";

  // functions.inc.php anpassen
  $functions = $dest.'/redaxo/include/functions.inc.php';
  $h = fopen($functions, 'r');
  $cont = fread($h, filesize($functions));
  fclose($h);

  echo '>> activate compatibility API'."\n";

  // compat klasse aktivieren
  $cont = str_replace(
    "// include_once \$REX['INCLUDE_PATH'].'/classes/class.compat.inc.php';",
    "include_once \$REX['INCLUDE_PATH'].'/classes/class.compat.inc.php';",
    $cont
  );

  $h = fopen($functions, 'w+');
  if (fwrite($h, $cont, strlen($cont)) > 0)
    fclose($h);

  echo '> fix addons.inc.php'."\n";

  // addons.inc.php anpassen
  $addons = $dest.'/redaxo/include/addons.inc.php';
  $h = fopen($addons, 'r');
  $cont = fread($h, filesize($addons));
  fclose($h);

  // Addons installieren
  $cont = ereg_replace("(\/\/.---.DYN.*\/\/.---.\/DYN)", "// --- DYN\n\n// --- /DYN", $cont);

  $h = fopen($addons, 'w+');
  if (fwrite($h, $cont, strlen($cont)) > 0)
    fclose($h);

  // Das kopierte Release-Script aus dem neu erstellten Release löschen
  unlink($dest .'/release.php');

  echo '>>> FINISHED'."\n";
}

/**
 * Returns the content of the given folder
 *
 * @param $dir Path to the folder
 * @return Array Content of the folder or false on error
 * @author Markus Staab <staab@public-4u.de>
 */
function readFolder($dir)
{
  if (!is_dir($dir))
  {
    trigger_error('Folder "' . $dir . '" is not available or not a directory');
    return false;
  }
  $hdl = opendir($dir);
  $folder = array ();
  while (false !== ($file = readdir($hdl)))
  {
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
 * @author Markus Staab <staab@public-4u.de>
 */

function readFilteredFolder($dir, $fileprefix)
{
  $filtered = array ();
  $folder = readFolder($dir);

  if (!$folder)
  {
    return false;
  }

  foreach ($folder as $file)
  {
    if (endsWith($file, $fileprefix))
    {
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
 * @author Markus Staab <staab@public-4u.de>
 */
function readFolderFiles($dir, $except = array ())
{
  $folder = readFolder($dir);
  $files = array ();

  if (!$folder)
  {
    return false;
  }

  foreach ($folder as $file)
  {
    if (is_file($dir . '/' . $file) && !in_array($file, $except))
    {
      $files[] = $file;
    }
  }

  return $files;
}

/**
 * Returns the subfolders of the given folder
 *
 * @param $dir Path to the folder
 * @param $ignore_dots True if the system-folders "." and ".." should be ignored
 * @return Array Subfolders of the folder or false on error
 * @author Markus Staab <staab@public-4u.de>
 */
function readSubFolders($dir, $ignore_dots = true)
{
  $folder = readFolder($dir);
  $folders = array ();

  if (!$folder)
  {
    return false;
  }

  foreach ($folder as $file)
  {
    if ($ignore_dots && ($file == '.' || $file == '..'))
    {
      continue;
    }
    if (is_dir($dir . '/' . $file))
    {
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

  if(is_array($subdirs))
  {
    foreach ($subdirs as $key => $subdir)
    {
      if (in_array($subdir, $except))
      {
        unset($subdirs[$key]);
        continue;
      }

      _readFolderStructure($dir .'/'. $subdir, $except, $result);
    }
  }

  $result[$dir] = array_merge($files, $subdirs);

  return $result;
}

function sortFolderStructure($path1, $path2)
{
  return strlen($path1) > strlen($path2) ? 1 : -1;
}
?>