<?php

/**
 * Addon Funktionen
 * @package redaxo4
 * @version $Id: function_rex_addons.inc.php,v 1.3 2008/02/25 09:51:18 kills Exp $
 */

function rex_plugins_dir($plugin = null)
{
  if($plugin)
    return (dirname(dirname(__FILE__))) . '/plugins/'. $plugin .'/';

  return (dirname(dirname(__FILE__))) . '/plugins/';
}

function rex_plugins_file()
{
  return (dirname(dirname(__FILE__))) . '/plugins.inc.php';
}

function rex_install_plugin($plugins, $pluginname, $installDump = true)
{
  global $REX, $I18N_COM;
  $state = true;

  $install_dir = rex_plugins_dir() .$pluginname;
  $install_file = $install_dir.'/install.inc.php';
  $install_sql = $install_dir.'/install.sql';
  $config_file = $install_dir.'/config.inc.php';

  // Prüfen des Addon Ornders auf Schreibrechte,
  // damit das Addon später wieder gelöscht werden kann
  $state = rex_is_writable($install_dir);

  if ($state === true)
  {
    if (is_readable($install_file))
    {
      require $install_file;

      // Wurde das "install" Flag gesetzt, oder eine Fehlermeldung ausgegeben? Wenn ja, Abbruch
      $instmsg = OOPlugin :: getProperty($pluginname, 'installmsg', '');
      if (!OOPlugin :: isInstalled($pluginname) || $instmsg)
      {
        $state = $I18N_COM->msg('plugin_no_install', $pluginname).'<br />';
        if ($instmsg == '')
        {
          $state .= $I18N_COM->msg('plugin_no_reason');
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
          if (!OOPlugin :: isActivated($pluginname))
          {
            require $config_file;
          }
        }
        else
        {
          $state = $I18N_COM->msg('plugin_config_not_found');
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
          $state = rex_generatePlugins($plugins);
        }
      }
    }
    else
    {
      $state = $I18N_COM->msg('plugin_install_not_found');
    }
  }

  if($state !== true)
    OOPlugin::setProperty($pluginname, 'install', 0);

  return $state;
}

function rex_activate_plugin($plugins, $pluginname)
{
  global $I18N_COM;
  $state = true;

  if (OOPlugin :: isInstalled($pluginname))
  {
    OOPlugin::setProperty($pluginname, 'status', 1);
    // regenerate Addons file
    $state = rex_generatePlugins($plugins);
  }
  else
  {
    $state = $I18N_COM->msg('plugin_no_activation', $pluginname);
  }

  if($state !== true)
    OOPlugin::setProperty($pluginname, 'status', 0);

  return $state;
}

function rex_deactivate_plugin($plugins, $pluginname)
{
  $state = true;

  OOPlugin::setProperty($pluginname, 'status', 0);

  // regenerate Addons file
  $state = rex_generatePlugins($plugins);

  return $state;
}

function rex_uninstall_plugin($plugins, $pluginname)
{
  global $REX, $I18N_COM;

  $state = true;
  $install_dir = rex_plugins_dir() .'/'. $pluginname;
  $uninstall_file = $install_dir.'/uninstall.inc.php';
  $uninstall_sql = $install_dir.'/uninstall.sql';

  if (is_readable($uninstall_file))
  {
    require $uninstall_file;

    // Wurde das "uninstall" Flag gesetzt, oder eine Fehlermeldung ausgegeben? Wenn ja, Abbruch
    $instmsg = OOPlugin :: getProperty($pluginname, 'installmsg', '');
    if (OOPlugin :: isInstalled($pluginname) || $instmsg)
    {
      $state = $I18N_COM->msg('plugin_no_uninstall', $pluginname).'<br/>';
      if (empty($instmsg))
      {
        $state .= $I18N_COM->msg('plugin_no_reason');
      }
      else
      {
        $state .= $instmsg;
      }
    }
    else
    {
      $state = rex_deactivate_plugin($plugins, $pluginname);

		  if($state === true && is_readable($uninstall_sql))
		  {
				$state = rex_install_dump($uninstall_sql);

        if($state !== true)
          $state = 'Error found in uninstall.sql:<br />'. $state;
			}

      if ($state === true)
      {
        // regenerate Addons file
        $state = rex_generatePlugins($plugins);
      }
    }
  }
  else
  {
    $state = $I18N_COM->msg("plugin_uninstall_not_found");
  }

  // Fehler beim uninstall -> Addon bleibt installiert
  if($state !== true)
    OOPlugin::setProperty($pluginname, 'install', 1);

  return $state;
}

function rex_read_plugins_folder($folder = '')
{
  global $REX;

  if ($folder == '')
  {
    $folder = rex_plugins_dir();
  }

  $plugins = array ();
  $hdl = opendir($folder);
  while (($file = readdir($hdl)) !== false)
  {
    if($file == '.' || $file == '..') continue;

    if (is_dir($folder.$file))
    {
      $plugins[] = $file;
    }
  }
  closedir($hdl);

  // Sortiere Array
  natsort($plugins);

  return $plugins;
}

function rex_generatePlugins($plugins)
{
  global $REX;
  natsort($plugins);

  $content = "";
  foreach ($plugins as $cur)
  {
    if (!OOPlugin :: isInstalled($cur))
      OOPlugin::setProperty($cur, 'install', 0);

    if (!OOPlugin :: isActivated($cur))
      OOPlugin::setProperty($cur, 'status', 0);

    $content .= sprintf(
      "%s = %d;\n%s = %d;\n\n",
      OOPlugin::getAsPropertyString($cur, 'install'),
      OOPlugin::getProperty($cur, 'install'),
      OOPlugin::getAsPropertyString($cur, 'status'),
      OOPlugin::getProperty($cur, 'status')
    );
  }

  $content .= sprintf(
    "\$REX['ADDON']['pluginlist']['community'] = \"%s\";",
    implode(',', OOPlugin::getRegisteredPlugins())
  );

  // Da dieser Funktion öfter pro request aufgerufen werden kann,
  // hier die caches löschen
  clearstatcache();

  $file = rex_plugins_file();
  if(!rex_replace_dynamic_contents($file, $content))
  {
    return 'Datei "'.$file.'" hat keine Schreibrechte';
  }
  return true;
}

?>