<?php

$error = '';

if (!extension_loaded('gd')) {
   if (!dl('gd.so')) {
       $error = 'GD-LIB-extension not available! See <a href="http://www.php.net/gd">http://www.php.net/gd</a>'; 
   }
}

if ($error != '')
{
  $REX['ADDON']['installmsg']['image_resize'] = $error;
}
else
{
  $REX['ADDON']['install']['image_resize'] = true;
}

?>