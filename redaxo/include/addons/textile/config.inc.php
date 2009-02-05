<?php

/**
 * Textile Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * 
 * @package redaxo4
 * @version $Id: config.inc.php,v 1.5 2008/03/11 16:04:25 kills Exp $
 */

$mypage = 'textile';

$REX['ADDON']['rxid'][$mypage] = '79';
$REX['ADDON']['page'][$mypage] = $mypage;
$REX['ADDON']['name'][$mypage] = 'Textile';
$REX['ADDON']['perm'][$mypage] = 'textile[]';
$REX['ADDON']['version'][$mypage] = "1.2";
$REX['ADDON']['author'][$mypage] = "Markus Staab, Dean Allen www.textism.com";
$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';

$REX['PERM'][] = 'textile[]';
$REX['EXTPERM'][] = 'textile[help]';

$I18N_A79 = new i18n($REX['LANG'], $REX['INCLUDE_PATH'].'/addons/'.$mypage.'/lang/');

require_once($REX['INCLUDE_PATH']. '/addons/textile/classes/class.textile.inc.php');
require_once $REX['INCLUDE_PATH']. '/addons/textile/functions/function_textile.inc.php';

if ($REX['REDAXO'])
{
  require_once $REX['INCLUDE_PATH'].'/addons/textile/functions/function_help.inc.php';

  if(rex_get('css', 'string') == 'addons/'. $mypage)
  {
    $cssfile = $REX['INCLUDE_PATH'] .'/addons/'. $mypage .'/css/textile.css';
    rex_send_file($cssfile, 'text/css');
    exit();
  }

  rex_register_extension('PAGE_HEADER',
    create_function('$params', 'return $params[\'subject\'] .\'  <link rel="stylesheet" type="text/css" href="index.php?css=addons/'. $mypage .'" />\'."\n";')
  );
}