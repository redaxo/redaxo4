<?php

/**
 * Klasse zum prüfen ob Plugins installiert/aktiviert sind
 * @package redaxo4
 * @version $Id: class.ooaddon.inc.php,v 1.5 2008/02/22 20:07:31 kills Exp $
 */

class OOPlugin extends rex_addon
{
  /**
   * @override
   * @see redaxo/include/classes/rex_addon#isAvailable($addon)
   */
  function isAvailable($addon, $plugin, $default = null)
  {
    return parent::isAvailable(array($addon, $plugin), $default);
  }

  /**
   * @override
   * @see redaxo/include/classes/rex_addon#isActivated($addon)
   */
  function isActivated($addon, $plugin, $default = null)
  {
    return parent::isActivated(array($addon, $plugin), $default);
  }

  /**
   * @override
   * @see redaxo/include/classes/rex_addon#isInstalled($addon)
   */
  function isInstalled($addon, $plugin, $default = null)
  {
    return parent::isInstalled(array($addon, $plugin), $default);
  }

  /**
   * @override
   * @see redaxo/include/classes/rex_addon#getSupportPage($addon, $default)
   */
  function getSupportPage($addon, $plugin, $default = null)
  {
    return parent::getSupportPage(array($addon, $plugin), $default);
  }
  
  /**
   * @override
   * @see redaxo/include/classes/rex_addon#getVersion($addon, $default)
   */
  function getVersion($addon, $plugin, $default = null)
  {
    return parent::getVersion(array($addon, $plugin), $default);
  }
  
  /**
   * @override
   * @see redaxo/include/classes/rex_addon#getAuthor($addon, $default)
   */
  function getAuthor($addon, $plugin, $default = null)
  {
    return parent::getAuthor(array($addon, $plugin), $default);
  }
  
  /**
   * @override
   * @see redaxo/include/classes/rex_addon#getProperty($addon, $property, $default)
   */
  function getProperty($addon, $plugin, $property, $default = null)
  {
    return parent::getProperty(array($addon, $plugin), $property, $default);
  }
  
  /**
   * @override
   * @see redaxo/include/classes/rex_addon#setProperty($addon, $property, $value)
   */
  function setProperty($addon, $plugin, $property, $value)
  {
    return parent::setProperty(array($addon, $plugin), $property, $value);
  }
  
  /**
   * Gibt ein Array aller verfügbaren Plugins zurück.
   * 
   * @param string $addon Name des Addons
   * 
   * @return array Array aller verfügbaren Plugins
   */
  function getAvailablePlugins($addon)
  {
    global $REX;

    $plugins = array();
    if(isset($REX['ADDON']) && is_array($REX['ADDON']) &&
       isset($REX['ADDON']['plugins']) && is_array($REX['ADDON']['plugins']) &&
       isset($REX['ADDON']['plugins'][$addon]) && is_array($REX['ADDON']['plugins'][$addon]) &&
       isset($REX['ADDON']['plugins'][$addon]['status']) && is_array($REX['ADDON']['plugins'][$addon]['status']))
    {
      $plugins = $REX['ADDON']['plugins'][$addon]['status'];
    }
    
    $avail = array();
    foreach($plugins as $pluginName => $pluginStatus)
    {
      if(parent::getProperty($addon, $plugin, 'status', false))
      {
        $avail[] = $pluginName;
      }
    }

    return $avail;
  }
  
  /**
   * Gibt ein Array aller registrierten Plugins zurück.
   * Ein Plugin ist registriert, wenn es dem System bekannt ist (plugins.inc.php).
   * 
   * @param string $addon Name des Addons
   * 
   * @return array Array aller registrierten Plugins
   */
  function getRegisteredPlugins($addon)
  {
    global $REX;

    $plugins = array();
    if(isset($REX['ADDON']) && is_array($REX['ADDON']) &&
       isset($REX['ADDON']['plugins']) && is_array($REX['ADDON']['plugins']) &&
       isset($REX['ADDON']['plugins'][$addon]) && is_array($REX['ADDON']['plugins'][$addon]) &&
       isset($REX['ADDON']['plugins'][$addon]['install']) && is_array($REX['ADDON']['plugins'][$addon]['install']))
    {
      $plugins = array_keys($REX['ADDON']['plugins'][$addon]['install']);
    }
    
    return $plugins;
  }
}
