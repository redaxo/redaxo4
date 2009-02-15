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

function rex_install_plugin($PLUGINS, $addonname, $pluginname, $installDump = true)
{
  global $REX, $I18N;
  $state = true;

  $install_dir = rex_plugins_folder($addonname, $pluginname);
  $install_file = $install_dir .'install.inc.php';
  $install_sql  = $install_dir .'install.sql';
  $config_file  = $install_dir .'config.inc.php';

  // Prüfen des Addon Ornders auf Schreibrechte,
  // damit das Addon später wieder gelöscht werden kann
  $state = rex_is_writable($install_dir);

  if ($state === true)
  {
    if (is_readable($install_file))
    {
      $ADDONSsic = $REX['ADDON'];
      $REX['ADDON'] = array();
      
      require $install_file;
      
      $ADDONSsic['plugins'][$addonname] = $REX['ADDON'];
      $REX['ADDON'] = $ADDONSsic;
      unset($ADDONSsic);
      
      // Wurde das "install" Flag gesetzt, oder eine Fehlermeldung ausgegeben? Wenn ja, Abbruch
      $instmsg = OOPlugin :: getProperty($addonname, $pluginname, 'installmsg', '');
      
      if (!OOPlugin :: isInstalled($addonname, $pluginname) || $instmsg)
      {
        $state = $I18N->msg('plugin_no_install', $pluginname).'<br />';
        if ($instmsg == '')
        {
          $state .= $I18N->msg('plugin_no_reason');
        }
        else
        {
          $state .= $instmsg;
        }
      }
      else
      {
        // check if config file exists
        if (is_readable($config_file))
        {
          if (!OOPlugin :: isActivated($addonname, $pluginname))
          {
            require $config_file;
          }
        }
        else
        {
          $state = $I18N->msg('plugin_config_not_found');
        }

        if($installDump === true && $state === true && is_readable($install_sql))
			  {
					$state = rex_install_dump($install_sql);

          if($state !== true)
            $state = 'Error found in install.sql:<br />'. $state;
				}

        // Installation ok
        if ($state === true)
        {
          // regenerate Addons file
          $state = rex_generatePlugins($PLUGINS);
        }
      }
    }
    else
    {
      $state = $I18N->msg('plugin_install_not_found');
    }
  }

  if($state !== true)
    OOPlugin::setProperty($addonname, $pluginname, 'install', 0);

  return $state;
}

function rex_activate_plugin($PLUGINS, $addonname, $pluginname)
{
  global $I18N;
  $state = true;

  if (OOPlugin :: isInstalled($addonname, $pluginname))
  {
    OOPlugin::setProperty($addonname, $pluginname, 'status', 1);
    // regenerate Addons file
    $state = rex_generatePlugins($PLUGINS);
  }
  else
  {
    $state = $I18N->msg('plugin_no_activation', $pluginname);
  }

  if($state !== true)
    OOPlugin::setProperty($addonname, $pluginname, 'status', 0);

  return $state;
}

function rex_deactivate_plugin($PLUGINS, $addonname, $pluginname)
{
  $state = true;

  OOPlugin::setProperty($addonname, $pluginname, 'status', 0);

  // regenerate Addons file
  $state = rex_generatePlugins($PLUGINS);

  return $state;
}

function rex_uninstall_plugin($PLUGINS, $addonname, $pluginname)
{
  global $REX, $I18N;

  $state = true;
  $install_dir = rex_plugins_folder($addonname, $pluginname);
  $uninstall_file = $install_dir.'uninstall.inc.php';
  $uninstall_sql = $install_dir.'uninstall.sql';

  if (is_readable($uninstall_file))
  {
    $ADDONSsic = $REX['ADDON'];
    $REX['ADDON'] = array();
    
    require $uninstall_file;
    
    $ADDONSsic['plugins'][$addonname] = $REX['ADDON'];
    $REX['ADDON'] = $ADDONSsic;
    unset($ADDONSsic);
    
    // Wurde das "uninstall" Flag gesetzt, oder eine Fehlermeldung ausgegeben? Wenn ja, Abbruch
    $instmsg = OOPlugin :: getProperty($addonname, $pluginname, 'installmsg', '');
    if (OOPlugin :: isInstalled($addonname, $pluginname) || $instmsg)
    {
      $state = $I18N->msg('plugin_no_uninstall', $pluginname).'<br />';
      if (empty($instmsg))
      {
        $state .= $I18N->msg('plugin_no_reason');
      }
      else
      {
        $state .= $instmsg;
      }
    }
    else
    {
      $state = rex_deactivate_plugin($PLUGINS, $addonname, $pluginname);

		  if($state === true && is_readable($uninstall_sql))
		  {
				$state = rex_install_dump($uninstall_sql);

        if($state !== true)
          $state = 'Error found in uninstall.sql:<br />'. $state;
			}

      if ($state === true)
      {
        // regenerate Addons file
        $state = rex_generatePlugins($PLUGINS);
      }
    }
  }
  else
  {
    $state = $I18N->msg("plugin_uninstall_not_found");
  }

  // Fehler beim uninstall -> Addon bleibt installiert
  if($state !== true)
    OOPlugin::setProperty($addonname, $pluginname, 'install', 1);

  return $state;
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
  natsort($PLUGINS);

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

function rex_include_plugin($addonname, $pluginname)
{
  
}