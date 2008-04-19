<?php

/**
 * TinyMCE Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * 
 *
 * @author Dave Holloway
 * @author <a href="http://www.GN2-Netwerk.de">www.GN2-Netwerk.de</a>s
 *
 * @package redaxo4
 * @version $Id: install.inc.php,v 1.5 2008/03/11 16:04:53 kills Exp $
 */

require_once $REX['INCLUDE_PATH'] . '/addons/tinymce/functions/function_pclzip.inc.php';

$I18N_A52 = new i18n($REX['LANG'], $REX['INCLUDE_PATH'].'/addons/tinymce/lang/');


$error = '';

// check folder write permissions
$tmpDir = $REX['MEDIAFOLDER'].'/'. $REX['TEMP_PREFIX'];
if(!is_dir($tmpDir) && !mkdir($tmpDir))
  $error = 'Could not create temp-dir "'. $tmpDir .'"!';

if($error == '' && !is_writable($tmpDir))
  $error = 'temp-dir "'. $tmpDir .'" not writable!';

$tinyDir = $tmpDir.'/tinymce';
if($error == '' && !is_dir($tinyDir) && !mkdir($tinyDir))
  $error = 'Could not create tiny-dir "'. $tinyDir .'"!';

if($error == '' && !is_writable($tinyDir))
  $error = 'tiny-dir "'. $tinyDir .'" not writable!';

// Copy files
if($error == '')
{
  // Install Tiny Core
  rex_a52_extract_archive('include/addons/tinymce/js/tinymce.zip', $I18N_A52->msg('install_core'));
  // Install German Language Pack
  rex_a52_extract_archive('include/addons/tinymce/js/tinymce_lang_de.zip', $I18N_A52->msg('install_lang_pakage'));
  // Install Redaxo Plugin
  rex_a52_extract_archive('include/addons/tinymce/js/redaxo_tiny_plugin.zip', $I18N_A52->msg('install_redaxo_plugin'),'../files/'. $REX['TEMP_PREFIX'] .'/tinymce/jscripts/tiny_mce/plugins/');

  copy('include/addons/tinymce/css/tinymce.css', '../files/'. $REX['TEMP_PREFIX'] .'/tinymce/tinymce.css');
}

if($error != '')
  $REX['ADDON']['installmsg']['tinymce'] = $error;
else
  $REX['ADDON']['install']['tinymce'] = true;

?>