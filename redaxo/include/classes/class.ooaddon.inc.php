<?php

/**
 * Klasse zum pr黤en ob Addons installiert/aktiviert sind
 * @package redaxo4
 * @version svn:$Id$
 */

class OOAddon extends rex_addon
{
  /*
   * Pr黤t, ob ein System-Addon vorliegt
   *
   * @param string $addon Name des Addons
   *
   * @return boolean TRUE, wenn es sich um ein System-Addon handelt, sonst FALSE
   */
  static /*public*/ function isSystemAddon($addon)
  {
    global $REX;
    return in_array($addon, $REX['SYSTEM_ADDONS']);
  }

  /**
   * Gibt ein Array von verf黦baren Addons zur點k.
   *
   * @return array Array der verf黦baren Addons
   */
  static /*public*/ function getAvailableAddons()
  {
    $avail = array();
    foreach(OOAddon::getRegisteredAddons() as $addonName)
    {
      if(OOAddon::isAvailable($addonName))
        $avail[] = $addonName;
    }

    return $avail;
  }

  /**
   * Gibt ein Array aller registrierten Addons zur點k.
   * Ein Addon ist registriert, wenn es dem System bekannt ist (addons.inc.php).
   *
   * @return array Array aller registrierten Addons
   */
  static /*public*/ function getRegisteredAddons()
  {
    global $REX;

    $addons = array();
    if(isset($REX['ADDON']) && is_array($REX['ADDON']) &&
       isset($REX['ADDON']['install']) && is_array($REX['ADDON']['install']))
    {
      $addons = array_keys($REX['ADDON']['install']);
    }

    return $addons;
  }
}