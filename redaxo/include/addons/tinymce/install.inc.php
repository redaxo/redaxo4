<?php

/**
 * TinyMCE Addon
 *
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 *
 * @author Dave Holloway
 * @author <a href="http://www.GN2-Netwerk.de">www.GN2-Netwerk.de</a>s
 *
 * @package redaxo4
 * @version $Id$
 */

require_once $REX['INCLUDE_PATH'] . '/addons/tinymce/functions/function_pclzip.inc.php';

$I18N_A52 = new i18n($REX['LANG'], $REX['INCLUDE_PATH'].'/addons/tinymce/lang/');

// Install Tiny Core
rex_a52_extract_archive('include/addons/tinymce/js/tinymce.zip', $I18N_A52->msg('install_core'));
// Install German Language Pack
rex_a52_extract_archive('include/addons/tinymce/js/tinymce_lang_de.zip', $I18N_A52->msg('install_lang_pakage'));
// Install Redaxo Plugin
rex_a52_extract_archive('include/addons/tinymce/js/redaxo_tiny_plugin.zip', $I18N_A52->msg('install_redaxo_plugin'),'../files/tmp_/tinymce/jscripts/tiny_mce/plugins/');

copy('include/addons/tinymce/css/tinymce.css', '../files/tmp_/tinymce/tinymce.css');

$REX['ADDON']['install']['tinymce'] = true;

?>