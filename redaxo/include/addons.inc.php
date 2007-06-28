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


if(isset($REX['ADDON']) && is_array($REX['ADDON']))
{
  for($i=0;$i<count($REX['ADDON']['status']);$i++)
  {
  	if (current($REX['ADDON']['status']) == 1) include $REX['INCLUDE_PATH']."/addons/".key($REX['ADDON']['status'])."/config.inc.php";
  	next($REX['ADDON']['status']);
  }
}

// ----- all addons configs included
rex_register_extension_point( 'ADDONS_INCLUDED');

?>