<?php
class OOAddon
{
  function isAvailable($addon)
  {
    return OOAddon::isInstalled($addon) && OOAddon::isActivated($addon);
  }

  function isActivated($addon)
  {
    global $REX;
    return isset( $REX['ADDON']['status'][$addon]) && $REX['ADDON']['status'][$addon] == 1;
  }
  function isInstalled($addon)
  {
    global $REX;
    return isset( $REX['ADDON']['install'][$addon]) && $REX['ADDON']['install'][$addon] == 1;
  }
}
?>