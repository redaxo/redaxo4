<?php

/**
 * ImageMagick pdf2img Plugin for "Image Manager"
 *
 * @package redaxo 4.4.x/4.5.x
 * @version 1.0
 */

$myself = 'pdf2img';

if(!function_exists('exec')) {
  $REX['ADDON']['installmsg'][$myself] = '<br />PHP function <code>exec()</code> is disabled.';
  $REX['ADDON']['install'][$myself] = 0;
  return;
}

$cmd = 'which convert';
exec($cmd, $out ,$ret);
if($ret == 1) {
    $REX['ADDON']['installmsg'][$myself] = '<br />Could not determine path to <code>convert</code> using cmd "<code>which convert</code>" ..<br />most likely <code>Imagemagick</code> is not available on your server.';
  $REX['ADDON']['install'][$myself] = 0;
  return;
}

$REX['ADDON']['install'][$myself] = 1;
