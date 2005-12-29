<?php


/**
 * Addon Funktionen 
 * @package redaxo3
 * @version $Id$
 */

function rex_install_addon($addons, $addonname)
{
  global $REX, $I18N;
  $state = true;

  $install_dir = $REX['INCLUDE_PATH']."/addons/$addonname";
  $install_file = $install_dir."/install.inc.php";
  $config_file = $install_dir."/config.inc.php";

  // Prüfen des Addon Ornders auf Schreibrechte, 
  // damit das Addon später wieder gelöscht werden kann
  $state = rex_is_writable($install_dir);

  if ($state === true)
  {
    if (is_readable($install_file))
    {
      include $install_file;
      // Wurde das "install" Flag gesetzt, oder eine Fehlermeldung ausgegeben? Wenn ja, Abbruch
      if (!OOAddon :: isInstalled($addonname) || !empty( $REX['ADDON']['installmsg'][$addonname]))
      {
        $state = $I18N->msg("addon_no_install", $addonname)."<br/>";
        if ($REX['ADDON']['installmsg'][$addonname] == "")
        {
          $state .= $I18N->msg("addon_no_reason");
        }
        else
        {
          $state .= $REX['ADDON']['installmsg'][$addonname];
        }
      }
      else
      {
        // check if config file exists
        if (is_readable($config_file))
        {
          // skip config if it is a reinstall !
          if (!OOAddon :: isActivated($addonname))
          {
            // if config is broken installation prozess will be terminated -> no install -> no errors in redaxo
            include $config_file;
          }
        }
        else
        {
          $state = $I18N->msg("addon_config_not_found");
        }

        // Installation ok
        if ($state === true)
        {
          // regenerate Addons file
          $state = rex_generateAddons($addons);
        }
      }
    }
    else
    {
      $state = $I18N->msg("addon_install_not_found");
    }
  }

  return $state;
}

function rex_activate_addon($addons, $addonname)
{
  global $REX, $I18N;
  $state = true;

  if (OOAddon :: isInstalled($addonname))
  {
    $REX['ADDON']['status'][$addonname] = 1;
    // regenerate Addons file
    $state = rex_generateAddons($addons);
  }
  else
  {
    $state = $I18N->msg("addon_no_activation", $addonname);
  }

  return $state;
}

function rex_deactivate_addon($addons, $addonname)
{
  global $REX;
  $state = true;

  $REX['ADDON']['status'][$addonname] = 0;
  // regenerate Addons file
  $state = rex_generateAddons($addons);

  return $state;
}

function rex_uninstall_addon($addons, $addonname, $regenerate_addons = true)
{
  global $REX, $I18N;

  $state = true;
  $uninstall_file = $REX['INCLUDE_PATH']."/addons/$addonname/uninstall.inc.php";

  if (is_readable($uninstall_file))
  {
    include $uninstall_file;

    // Wurde das "uninstall" Flag gesetzt, oder eine Fehlermeldung ausgegeben? Wenn ja, Abbruch
    if (OOAddon :: isInstalled($addonname) || !empty($REX['ADDON']['installmsg'][$addonname]))
    {
      $state = $I18N->msg('addon_no_uninstall', $addonname).'<br/>';
      if (empty( $REX['ADDON']['installmsg'][$addonname]))
      {
        $state .= $I18N->msg('addon_no_reason');
      }
      else
      {
        $state .= $REX['ADDON']['installmsg'][$addonname];
      }
    }
    else
    {
      $state = rex_deactivate_addon($addons, $addonname);
      
      if ($state === true && $regenerate_addons)
      {
        // regenerate Addons file
        $state = rex_generateAddons($addons);
      }
    }
  }
  else
  {
    $state = $I18N->msg("addon_uninstall_not_found");
  }

  return $state;
}

function rex_delete_addon($addons, $addonname)
{
  global $REX, $I18N;
  $state = true;

  // zuerst deinstallieren
  $state = rex_uninstall_addon($addons, $addonname, false);

  if ($state === true)
  {
    // bei erfolg, komplett löschen
    rex_deleteDir($REX['INCLUDE_PATH']."/addons/$addonname", true, false);
    // regenerate Addons file
    $state = rex_generateAddons($addons);
  }

  return $state;
}

function rex_read_addons_folder($folder = '')
{
  global $REX;

  if ($folder == '')
  {
    $folder = $REX['INCLUDE_PATH'].'/addons/';
  }

  $addons = array ();
  $hdl = opendir($folder);
  while (($file = readdir($hdl)) !== false)
  {
    if ($file != '.' && $file != '..' && is_dir($folder.$file.'/.'))
    {
      $addons[] = $file;
    }
  }
  closedir($hdl);

  // Sortiere Array
  natsort($addons);

  return $addons;
}
?>