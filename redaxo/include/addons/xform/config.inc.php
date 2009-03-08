<?php

/**
 * XForm 
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$mypage = 'xform';

/* Addon Parameter */
$REX['ADDON']['page'][$mypage] = $mypage;
$REX['ADDON']['name'][$mypage] = 'XForm';
$REX['ADDON']['perm'][$mypage] = 'xform[]';
$REX['ADDON']['version'][$mypage] = '1.2';
$REX['ADDON']['author'][$mypage] = 'Jan Kristinus';
$REX['PERM'][] = 'xform[]';

// standard ordner fuer klassen
$REX['ADDON']['xform']['classpaths']['value'] = array($REX['INCLUDE_PATH'].'/addons/xform/classes/value/');
$REX['ADDON']['xform']['classpaths']['validate'] = array($REX['INCLUDE_PATH'].'/addons/xform/classes/validate/');
$REX['ADDON']['xform']['classpaths']['action'] = array($REX['INCLUDE_PATH'].'/addons/xform/classes/action/');

// Basis Klasse rex_xform
include ($REX['INCLUDE_PATH'].'/addons/'.$mypage.'/classes/basic/class.rex_xform.inc.php');

if($REX['USER'] && $REX['USER'])
{
	$I18N->appendFile($REX['INCLUDE_PATH'].'/addons/'.$mypage.'/lang/');
	
	$REX['SUBPAGES'][$mypage] = array();
	$REX['SUBPAGES'][$mypage][] = array( '' , $I18N->msg("overview"));
	if ($REX['USER']->isAdmin() || $REX['USER']->isValueOf("rights","xform[]")) 
		$REX['SUBPAGES'][$mypage][] = array ('email_templates' , $I18N->msg("email_templates"));
	if ($REX['USER']->isAdmin() || $REX['USER']->isValueOf("rights","xform[]")) 
		$REX['SUBPAGES'][$mypage][] = array ('description' , $I18N->msg("description"));
	if ($REX['USER']->isAdmin() || $REX['USER']->isValueOf("rights","xform[]")) 
		$REX['SUBPAGES'][$mypage][] = array ('module' , $I18N->msg("install_module"));

}
