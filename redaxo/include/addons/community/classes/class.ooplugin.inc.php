<?php

/**
 * Klasse zum prüfen ob Plugins installiert/aktiviert sind
 * @package redaxo4
 * @version svn:$Id$
 */

class OOPlugin
{
  function isAvailable($plugin)
  {
    return OOPlugin::isInstalled($plugin) && OOAddon::isActivated($plugin);
  }

  function isActivated($plugin)
  {
    return OOPlugin::getProperty($plugin, 'status') == 1;
  }

  function isInstalled($plugin)
  {
    return OOPlugin::getProperty($plugin, 'install') == 1;
  }

  function getAvailablePlugins()
  {
    global $REX;

    $plugins = OOPlugin::getRegisteredPlugins();
    $avail = array();
    foreach($plugins as $plugin)
    {
      if(OOPlugin::getProperty($plugin, 'status', false))
      {
        $avail[] = $plugin;
      }
    }

    return $avail;
  }

  function getRegisteredPlugins()
  {
    $plugins = OOAddon::getProperty('community', 'pluginlist', array());

    if(is_string($plugins))
      $plugins = explode(',', $plugins);

    return $plugins;
  }

  function setProperty($plugin, $property, $value)
  {
    global $REX;

    // Plugin in Liste aufnehmen
    $plugins = OOPlugin::getRegisteredPlugins();
    if(!in_array($plugin, $plugins))
    {
      $plugins[$plugin] = $plugin;
      OOAddon::setProperty('community', 'pluginlist', $plugins);
    }

    if(!isset($REX['ADDON']['plugins']['community'][$plugin]))
      $REX['ADDON']['plugins']['community'][$plugin] = array();

    // Property fuer das Plugin setzen
    $REX['ADDON']['plugins']['community'][$plugin][$property] = $value;
  }

  function getProperty($plugin, $property, $default = null)
  {
    global $REX;

    // Property fuer das Plugin setzen
    if(isset($REX['ADDON']['plugins']['community'][$plugin][$property]))
      return $REX['ADDON']['plugins']['community'][$plugin][$property];

    return $default;
  }

  function getAsPropertyString($plugin, $property)
  {
    return "\$REX['ADDON']['plugins']['community']['". $plugin ."']['". $property ."']";
  }
}
?>