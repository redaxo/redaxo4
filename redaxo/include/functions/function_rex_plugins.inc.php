<?php

/**
 * Addon Funktionen
 * @package redaxo4
 * @version $Id: function_rex_addons.inc.php,v 1.3 2008/02/25 09:51:18 kills Exp $
 */

function rex_plugins_folder($addon, $plugin = null)
{
  $addonFolder = rex_addons_folder($addon);
  
  if($plugin)
    return $addonFolder. 'plugins' .DIRECTORY_SEPARATOR. $plugin .DIRECTORY_SEPARATOR;

  return $addonFolder. 'plugins' .DIRECTORY_SEPARATOR;
}

function rex_plugins_file()
{
  return (dirname(dirname(__FILE__))) . '/plugins.inc.php';
}

function rex_read_plugins_folder($addon, $folder = '')
{
  global $REX;
  $plugins = array ();

  if ($folder == '')
  {
    $folder = rex_plugins_folder($addon, '*');
  }
  
  $files = glob(rtrim($folder,DIRECTORY_SEPARATOR), GLOB_NOSORT);
  if(is_array($files))
  {
    foreach($files as $file)
    {
      $plugins[] = basename($file);
    }
  }
  
  // Sortiere Array
  natsort($plugins);
  
  return $plugins;
}

function rex_generatePlugins($PLUGINS)
{
  global $REX;
  
  $content = "";
  foreach ($PLUGINS as $addon => $_plugins)
  {
    foreach($_plugins as $plugin)
    {
      if (!OOPlugin :: isInstalled($addon, $plugin))
        OOPlugin::setProperty($addon, $plugin, 'install', 0);
  
      if (!OOPlugin :: isActivated($addon, $plugin))
        OOPlugin::setProperty($addon, $plugin, 'status', 0);
  
      foreach(array('install', 'status') as $prop)
      {
        $content .= sprintf(
          "\$REX['ADDON']['plugins']['%s']['%s']['%s'] = '%d';\n",
          $addon,
          $prop,
          $plugin,
          OOPlugin::getProperty($addon, $plugin, $prop)
        );
      }
      $content .= "\n";
    }
  }

  // Da dieser Funktion öfter pro request aufgerufen werden kann,
  // hier die caches löschen
  clearstatcache();

  $file = $REX['INCLUDE_PATH']."/plugins.inc.php";
  if(rex_replace_dynamic_contents($file, $content) === false)
  {
    return 'Datei "'.$file.'" hat keine Schreibrechte';
  }
  return true;
}
