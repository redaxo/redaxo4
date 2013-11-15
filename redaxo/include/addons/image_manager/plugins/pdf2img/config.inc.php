<?php

/**
 * ImageMagick pdf2img Plugin for "Image Manager"
 *
 * @package redaxo 4.4.x/4.5.x
 * @version 1.0
 */

$REX['ADDON']['version']['pdf2img']     = '1.0';
$REX['ADDON']['title']['pdf2img']       = 'pdf2img';
$REX['ADDON']['author']['pdf2img']      = 'jan Kristinus';
$REX['ADDON']['supportpage']['pdf2img'] = 'www.redaxo.org';

if(!class_exists('rex_image_manager_pdf2img')){
  require_once($REX['INCLUDE_PATH'].'/addons/image_manager/plugins/pdf2img/classes/class.rex_image_manager_pdf2img.inc.php');
}

rex_register_extension('IMAGE_MANAGER_INIT','rex_image_manager_pdf2img::init');

