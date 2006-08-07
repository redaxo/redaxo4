<?php
$error = '';

if (!extension_loaded('gd'))
{
  if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
  {
    $ext_loaded = @ dl('gd.dll');
  }
  else
  {
    $ext_loaded = @ dl('gd.so');
  }
  if (!$ext_loaded)
  {
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