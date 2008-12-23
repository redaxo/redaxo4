<?php

/**
 * Klasse zum prüfen ob Addons installiert/aktiviert sind
 * @package redaxo4
 * @version $Id: class.ooaddon.inc.php,v 1.5 2008/02/22 20:07:31 kills Exp $
 */

class OOAddon
{
  function isAvailable($addon)
  {
    return OOAddon::isInstalled($addon) && OOAddon::isActivated($addon);
  }

  function isActivated($addon)
  {
    return OOAddon::getProperty($addon, 'status') == 1;
  }
  function isInstalled($addon)
  {
    return OOAddon::getProperty($addon, 'install') == 1;
  }

  function isSystemAddon($addon)
  {
    global $REX;
    return in_array($addon, $REX['SYSTEM_ADDONS']);
  }

  function getVersion($addon, $default = null)
  {
    return OOAddon::getProperty($addon, 'version', $default);
  }

  function getAuthor($addon, $default = null)
  {
    return OOAddon::getProperty($addon, 'author', $default);
  }

  function getSupportPage($addon, $default = null)
  {
    return OOAddon::getProperty($addon, 'supportpage', $default);
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
      $REX['ADDON'] = array();
      $REX['ADDON']['install'] = array();
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

  function setProperty($addon, $property, $value)
  {
    global $REX;

    if(!isset($REX['ADDON'][$property]))
      $REX['ADDON'][$property] = array();

    $REX['ADDON'][$property][$addon] = $value;
  }

  function getProperty($addon, $property, $default = null)
  {
    global $REX;

    return isset($REX['ADDON'][$property][$addon]) ? $REX['ADDON'][$property][$addon] : $default;
  }
}