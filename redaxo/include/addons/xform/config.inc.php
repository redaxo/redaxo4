<?php

/**
 * XO-Form 
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$mypage = 'xform';

/* Addon Parameter */
$REX['ADDON']['page'][$mypage] = $mypage;
$REX['ADDON']['name'][$mypage] = 'XForm';
$REX['ADDON']['perm'][$mypage] = 'xform[]';
$REX['ADDON']['version'][$mypage] = '1.0 rc2';
$REX['ADDON']['author'][$mypage] = 'Jan Kristinus';
$REX['PERM'][] = 'xform[]';

// standard ordner fuer klassen
$REX['ADDON']['xform']['classpaths']['value'] = array($REX['INCLUDE_PATH'].'/addons/xform/classes/value/');
$REX['ADDON']['xform']['classpaths']['validate'] = array($REX['INCLUDE_PATH'].'/addons/xform/classes/validate/');
$REX['ADDON']['xform']['classpaths']['action'] = array($REX['INCLUDE_PATH'].'/addons/xform/classes/action/');

// Basis Klasse rex_xform
include ($REX['INCLUDE_PATH'].'/addons/'.$mypage.'/classes/basic/class.rex_xform.inc.php');

?>