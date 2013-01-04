<?php

/**
 * Textile Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4
 * @version svn:$Id$
 */

$mypage = 'textile';

$REX['ADDON']['rxid'][$mypage] = '79';
$REX['ADDON']['name'][$mypage] = 'Textile';
$REX['ADDON']['perm'][$mypage] = 'textile[]';
$REX['ADDON']['version'][$mypage] = '1.5';
$REX['ADDON']['author'][$mypage] = "Markus Staab, Dean Allen www.textism.com, Steve (github.com/netcarver/textile)";
$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';

$REX['PERM'][] = 'textile[]';
$REX['EXTPERM'][] = 'textile[help]';

define('txt_has_unicode', rex_lang_is_utf8());
require_once($REX['INCLUDE_PATH']. '/addons/textile/vendor/classTextile.php');
require_once $REX['INCLUDE_PATH']. '/addons/textile/functions/function_textile.inc.php';

if ($REX['REDAXO'])
{
  require_once $REX['INCLUDE_PATH'].'/addons/textile/extensions/function_extensions.inc.php';
  require_once $REX['INCLUDE_PATH'].'/addons/textile/functions/function_help.inc.php';

  $I18N->appendFile($REX['INCLUDE_PATH'].'/addons/'.$mypage.'/lang/');

  rex_register_extension('PAGE_HEADER', 'rex_a79_css_add');
}
