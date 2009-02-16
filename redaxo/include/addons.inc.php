<?php

/**
 * Addonlist
 * @package redaxo4
 * @version $Id: addons.inc.php,v 1.3 2008/02/22 16:27:58 kills Exp $
 */

// ----------------- addons
unset($REX['ADDON']);

// ----------------- DONT EDIT BELOW THIS
// --- DYN

// --- /DYN
// ----------------- /DONT EDIT BELOW THIS

require $REX['INCLUDE_PATH']. '/plugins.inc.php';

foreach(OOAddon::getAvailableAddons() as $addonName)
{
  $addonConfig = rex_addons_folder($addonName). 'config.inc.php';
  if(file_exists($addonConfig))
  {
    require $addonConfig;
  }
  
  foreach(OOPlugin::getAvailablePlugins($addonName) as $pluginName)
  {
    $pluginConfig = rex_plugins_folder($addonName, $pluginName). 'config.inc.php';
    if(file_exists($pluginConfig))
    {
      require $pluginConfig;
    }
  }
}

// ----- all addons configs included
rex_register_extension_point('ADDONS_INCLUDED');