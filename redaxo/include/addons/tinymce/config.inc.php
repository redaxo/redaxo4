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
 * @version $Id: config.inc.php,v 1.4 2008/03/11 16:04:53 kills Exp $
 */

$mypage = 'tinymce';

$REX['ADDON']['rxid'][$mypage] = '52';
$REX['ADDON']['page'][$mypage] = $mypage;
$REX['ADDON']['name'][$mypage] = 'TinyMCE';
$REX['ADDON']['perm'][$mypage] = 'tiny_mce[]';
$REX['ADDON']['version'][$mypage] = '1.0';
$REX['ADDON']['author'][$mypage] = 'Wolfgang Hutteger, Markus Staab, Dave Holloway';
$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';

$I18N_A52 = new i18n($REX['LANG'], $REX['INCLUDE_PATH'].'/addons/'.$mypage.'/lang/');

// Include tinylib
if($REX['REDAXO'])
{
  $cssLink = '  <link rel="stylesheet" type="text/css" href="../files/tmp_/tinymce/tinymce.css" />'."\n";
  rex_register_extension('PAGE_HEADER', create_function('$params', 'return $params[\'subject\'].\''. $cssLink .'\';'));

	include_once $REX['INCLUDE_PATH'].'/addons/tinymce/classes/class.tiny.inc.php';
}
?>