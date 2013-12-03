<?php

/**
 * ImageMagick convert2img Plugin for "Image Manager"
 *
 * @package redaxo 4.4.x/4.5.x
 * @version 1.0
 */

$REX['ADDON']['version']['convert2img']     = '1.0';
$REX['ADDON']['title']['convert2img']       = 'convert2img';
$REX['ADDON']['author']['convert2img']      = 'jan Kristinus';
$REX['ADDON']['supportpage']['convert2img'] = 'www.redaxo.org';

if(!class_exists('rex_image_manager_convert2img')){
  require_once($REX['INCLUDE_PATH'].'/addons/image_manager/plugins/convert2img/classes/class.rex_image_manager_convert2img.inc.php');
}

$REX['MEDIAPOOL']['IMAGE_EXTENSIONS']   = array_merge($REX['MEDIAPOOL']['IMAGE_EXTENSIONS'], rex_image_manager_convert2img::$convert_types);

rex_register_extension('IMAGE_MANAGER_INIT','rex_image_manager_convert2img::init');

