<?php

/**
 * Addonlist
 * @package redaxo3
 * @version $Id$
 */

// ----------------- addons
if (isset($REX['ADDON']['status'])) {
  unset($REX['ADDON']['status']);
}

// ----------------- DONT EDIT BELOW THIS
// --- DYN

// --- /DYN
// ----------------- /DONT EDIT BELOW THIS

if(!isset($REX['ADDON']) || !is_array($REX['ADDON']))
{
  $REX['ADDON'] = array();
  $REX['ADDON']['install'] = array();
  $REX['ADDON']['status'] = array();
}

foreach($REX['ADDON']['status'] as $addonName => $addonStatus)
{
  if($addonStatus == 1)
    include $REX['INCLUDE_PATH'].'/addons/'.$addonName.'/config.inc.php';
}

// ----- all addons configs included
rex_register_extension_point( 'ADDONS_INCLUDED');

?>