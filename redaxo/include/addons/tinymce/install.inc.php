<?php

//TinyMCE 2.0.6.1 Redaxo Mods by Dave Holloway @ GN2-Netwerk.de

require_once $REX['INCLUDE_PATH'] . '/addons/tinymce/functions/function_pclzip.inc.php';

// Install Tiny Core
rex_a52_extract_archive('include/addons/tinymce/js/tinymce.zip');
// Install German Language Pack
rex_a52_extract_archive('include/addons/tinymce/js/tinymce_lang_de.zip');

$REX['ADDON']['install']['tinymce'] = true;

?>