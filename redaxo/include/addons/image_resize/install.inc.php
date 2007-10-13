<?php
/**
 * Image-Resize Addon
 * 
 * @author office[at]vscope[dot]at Wolfgang Hutteger
 * @author <a href="http://www.vscope.at">www.vscope.at</a>
 * 
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * 
 * @package redaxo4
 * @version $Id$
 */

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