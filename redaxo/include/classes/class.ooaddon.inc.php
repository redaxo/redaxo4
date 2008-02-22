<?php

/**
 * Klasse zum prüfen ob Addons installiert/aktiviert sind
 * @package redaxo4
 * @version $Id$
 */

class OOAddon
{
  function isAvailable($addon)
  {
    return OOAddon::isInstalled($addon) && OOAddon::isActivated($addon);
  }

  function isActivated($addon)
  {
    return OOAddon::_getProperty($addon, 'status') == 1;
  }
  function isInstalled($addon)
  {
    return OOAddon::_getProperty($addon, 'install') == 1;
  }

  function isSystemAddon($addon)
  {
    global $REX;
    return in_array($addon, $REX['SYSTEM_ADDONS']);
  }

  function getVersion($addon, $default = null)
  {
    return OOAddon::_getProperty($addon, 'version', $default);
  }

  function getAuthor($addon, $default = null)
  {
    return OOAddon::_getProperty($addon, 'author', $default);
  }

  function getSupportPage($addon, $default = null)
  {
    return OOAddon::_getProperty($addon, 'supportpage', $default);
  }

  function getAvailableAddons()
  {
    global $REX;

    if(isset($REX['ADDON']) && is_array($REX['ADDON']) &&
       isset($REX['ADDON']['status']) && is_array($REX['ADDON']['status']))
    {
      $addons = $REX['ADDON']['status'];
    }
    else
    {
      $REX['ADDON']['status'] = array();
      $addons = array();
    }

    $avail = array();
    foreach($addons as $addonName => $addonStatus)
    {
      if($addonStatus == 1)
        $avail[] = $addonName;
    }

    return $avail;
  }

  function _getProperty($addon, $property, $default = null)
  {
    global $REX;
    return isset($REX['ADDON'][$property][$addon]) ? $REX['ADDON'][$property][$addon] : $default;
  }
}
?>