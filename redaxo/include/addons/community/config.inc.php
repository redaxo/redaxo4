<?php

// error_reporting(E_ALL ^ E_NOTICE);

$mypage = "community";        // only for this file

$REX['ADDON']['rxid'][$mypage] = '5';
$REX['ADDON']['page'][$mypage] = "$mypage";     // pagename/foldername
$REX['ADDON']['name'][$mypage] = "Community";   // name
$REX['ADDON']['perm'][$mypage] = "community[]"; // benoetigt mindest permission
$REX['ADDON']['version'][$mypage] = '1.2';
$REX['ADDON']['author'][$mypage] = 'Jan Kristinus';
$REX['ADDON']['supportpage'][$mypage] = 'community.redaxo.de';
$REX['PERM'][] = "community[]";

$I18N_COM = new i18n($REX['LANG'], $REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/lang');

// ----- Community User Funktionen
include $REX["INCLUDE_PATH"]."/addons/community/functions/functions.rex_com_user.inc.php";
include $REX["INCLUDE_PATH"]."/addons/community/functions/functions.rex_com_replace.inc.php";
include $REX["INCLUDE_PATH"]."/addons/community/functions/functions.rex_com_paginate.inc.php";
include $REX["INCLUDE_PATH"]."/addons/community/functions/functions.rex_com_formatter.inc.php";

if ($REX["REDAXO"] && $REX['USER'])
{
	$REX['EXTRAPERM'][] = "community[admin]";
	$REX['EXTRAPERM'][] = "community[users]";
	include $REX["INCLUDE_PATH"]."/addons/community/functions/functions.userconfig.inc.php";
	
	$REX['ADDON'][$mypage]['SUBPAGES'] = array();
	$REX['ADDON'][$mypage]['SUBPAGES'][] = array( '' , '&Uuml;bersicht');
	
	// Feste Subpages
	if ($REX['USER']->isAdmin() || $REX['USER']->isValueOf("rights","community[users]")) 
		$REX['ADDON'][$mypage]['SUBPAGES'][] = array ('user' , 'User Verwaltung');
	if ($REX['USER']->isAdmin() || $REX['USER']->isValueOf("rights","community[admin]")) 
		$REX['ADDON'][$mypage]['SUBPAGES'][] = array ('user_fields' , 'User Felder erweitern');
	
	// PlugIn Seiten einbauen..
	$plugins = OOPlugin::getAvailablePlugins('community');
	foreach($plugins as $plugin)
	{
		if ($REX['USER']->isAdmin("rights","admin[]") ||
		    $REX['USER']->isValueOf("rights","community[admin]") ||
		    $REX['USER']->isValueOf("rights","community[". $plugin ."]"))
	  {
		  $REX['ADDON'][$mypage]['SUBPAGES'][] = array('plugin.'.$plugin,"translate:$plugin");
	  }
	}
}

// ----- XForm values/action/validations einbinden
$REX['ADDON']['community']['xform_path']['value'] = array($REX['INCLUDE_PATH'].'/addons/community/xform/classes/value/');
$REX['ADDON']['community']['xform_path']['validate'] = array($REX['INCLUDE_PATH'].'/addons/community/xform/classes/validate/');
$REX['ADDON']['community']['xform_path']['action'] = array($REX['INCLUDE_PATH'].'/addons/community/xform/classes/action/');

rex_register_extension('ADDONS_INCLUDED', 'rex_com_xform_add');
function rex_com_xform_add($params){
	global $REX;
	foreach($REX['ADDON']['community']['xform_path']['value'] as $value)
	{
		$REX['ADDON']['xform']['classpaths']['value'][] = $value;
	}
	foreach($REX['ADDON']['community']['xform_path']['validate'] as $validate)
	{
		$REX['ADDON']['xform']['classpaths']['validate'][] = $validate;
	}
	foreach($REX['ADDON']['community']['xform_path']['action'] as $action)
	{
		$REX['ADDON']['xform']['classpaths']['action'][] = $action;
	}
}

