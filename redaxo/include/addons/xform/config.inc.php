<?php

/**
 * XForm 
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$mypage = 'xform';

/* Addon Parameter */
$REX['ADDON']['name'][$mypage] = 'XForm';
$REX['ADDON']['perm'][$mypage] = 'xform[]';
$REX['ADDON']['version'][$mypage] = '2.2';
$REX['ADDON']['author'][$mypage] = 'Jan Kristinus';
$REX['ADDON']['supportpage'][$mypage] = 'redaxo.yakamara.de';
$REX['PERM'][] = 'xform[]';

// standard ordner fuer klassen
$REX['ADDON']['xform']['classpaths']['value'] = array($REX['INCLUDE_PATH'].'/addons/xform/classes/value/');
$REX['ADDON']['xform']['classpaths']['validate'] = array($REX['INCLUDE_PATH'].'/addons/xform/classes/validate/');
$REX['ADDON']['xform']['classpaths']['action'] = array($REX['INCLUDE_PATH'].'/addons/xform/classes/action/');
$REX['ADDON']['xform']['classpaths']['action'][] = $REX["INCLUDE_PATH"]."/addons/xform/manager/classes/action/";
$REX['ADDON']['xform']['classpaths']['value'][] = $REX["INCLUDE_PATH"]."/addons/xform/manager/classes/value/";

include ($REX['INCLUDE_PATH'].'/addons/'.$mypage.'/classes/basic/class.rex_radio.inc.php');

// Basis Klasse rex_xform
include ($REX['INCLUDE_PATH'].'/addons/'.$mypage.'/classes/basic/class.rex_xform.inc.php');
include ($REX['INCLUDE_PATH'].'/addons/'.$mypage.'/classes/basic/class.rex_xform_emailtemplate.inc.php');
include ($REX['INCLUDE_PATH'].'/addons/'.$mypage.'/manager/classes/basic/class.rex_xform_manager.inc.php');
include ($REX['INCLUDE_PATH'].'/addons/'.$mypage.'/manager/functions/functions.inc.php');

if($REX['REDAXO'] && $REX['USER'])
{
	$I18N->appendFile($REX['INCLUDE_PATH'].'/addons/'.$mypage.'/lang/');
	
	$REX['ADDON'][$mypage]['SUBPAGES'] = array();
	$REX['ADDON'][$mypage]['SUBPAGES'][] = array( '' , $I18N->msg("xform_overview"));
	if ($REX['USER']->isAdmin() || $REX['USER']->hasPerm("xform[]")) 
		$REX['ADDON'][$mypage]['SUBPAGES'][] = array ('email_templates' , $I18N->msg("xform_email_templates"));
	if ($REX['USER']->isAdmin() || $REX['USER']->hasPerm("xform[]")) 
		$REX['ADDON'][$mypage]['SUBPAGES'][] = array ('description' , $I18N->msg("xform_description"));
	if ($REX['USER']->isAdmin() || $REX['USER']->hasPerm("xform[]")) 
		$REX['ADDON'][$mypage]['SUBPAGES'][] = array ('module' , $I18N->msg("xform_install_module"));
		
	function rex_xform_css($params){

		$params['subject'] .= "\n  ".'<link rel="stylesheet" type="text/css" href="../files/addons/xform/xform.css" media="screen, projection, print" />';
		$params['subject'] .= "\n  ".'<script src="../files/addons/xform/manager.js" type="text/javascript"></script>';

		return $params['subject'];
	}
	  
  rex_register_extension('PAGE_HEADER', 'rex_xform_css');

}
