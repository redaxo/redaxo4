<?php

/**
 * ImageMagick pdf2img Plugin for "Image Manager"
 *
 * @package redaxo 4.4.x/4.5.x
 * @version 1.0
 */

$error = '';
if ($error != '')
  $REX['ADDON']['installmsg']['pdf2img'] = $error;
else
  $REX['ADDON']['install']['pdf2img'] = 0;
