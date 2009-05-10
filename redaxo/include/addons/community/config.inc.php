<?php

$mypage = "community"; // only for this file

// ********** Allgemeine AddOn Config
$REX['ADDON']['rxid'][$mypage] = '5';
$REX['ADDON']['page'][$mypage] = "$mypage";     // pagename/foldername
$REX['ADDON']['name'][$mypage] = "Community";   // name
$REX['ADDON']['perm'][$mypage] = "community[]"; // benoetigt mindest permission
$REX['ADDON']['version'][$mypage] = '1.3';
$REX['ADDON']['author'][$mypage] = 'Jan Kristinus';
$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';
$REX['PERM'][] = "community[]";

$I18N_COM = new i18n($REX['LANG'], $REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/lang');

// ********** Community User Funktionen
include $REX["INCLUDE_PATH"]."/addons/community/functions/functions.rex_com_user.inc.php";
include $REX["INCLUDE_PATH"]."/addons/community/functions/functions.rex_com_replace.inc.php";
include $REX["INCLUDE_PATH"]."/addons/community/functions/functions.rex_com_paginate.inc.php";
include $REX["INCLUDE_PATH"]."/addons/community/functions/functions.rex_com_formatter.inc.php";

// ********** Backend, Perms, Subpages etc.
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
	
	if($REX["REDAXO"])
	{
		function rex_community_addCSS($params)
		{
		    echo "\n".'<link rel="stylesheet" type="text/css" href="../files/addons/community/community_be.css" media="screen, projection, print" />';
		}
		rex_register_extension('PAGE_HEADER', 'rex_community_addCSS');
	}
}

// ********** XForm values/action/validations einbinden
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