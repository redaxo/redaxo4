<?php

$mypage = "community"; // only for this file

// ---------- Allgemeine AddOn Config

if (isset($I18N) && is_object($I18N))
  $I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/lang');

$REX['ADDON']['name'][$mypage] = "Community";   // name
$REX['ADDON']['perm'][$mypage] = "community[]"; // benoetigte mindest permission
$REX['ADDON']['navigation'][$mypage] = array('block'=>'community');

$REX['ADDON']['version'][$mypage] = '2.2';
$REX['ADDON']['author'][$mypage] = 'Jan Kristinus';
$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';
$REX['PERM'][] = "community[]";


// ---------- Backend, Perms, Subpages etc.
if ($REX["REDAXO"] && $REX['USER'])
{
	$REX['EXTRAPERM'][] = "community[admin]";
	$REX['EXTRAPERM'][] = "community[users]";
	
	$REX['ADDON'][$mypage]['SUBPAGES'] = array();
	$REX['ADDON'][$mypage]['SUBPAGES'][] = array( '' , '&Uuml;bersicht');
	
	if ($REX['USER']->isAdmin() || $REX['USER']->hasPerm("community[users]")) 
		$REX['ADDON'][$mypage]['SUBPAGES'][] = array ('user' , $I18N->msg('com_user_management'));
	if ($REX['USER']->isAdmin() || $REX['USER']->hasPerm("community[admin]")) 
		$REX['ADDON'][$mypage]['SUBPAGES'][] = array ('field' , $I18N->msg('com_field_management'));
	
}


// ---------- XForm values/action/validations einbinden

$REX['ADDON']['community']['xform_path']['value'] = array();
$REX['ADDON']['community']['xform_path']['validate'] = array();
$REX['ADDON']['community']['xform_path']['action'] = array();

$REX['ADDON']['community']['xform_path']['value'][] = $REX["INCLUDE_PATH"]."/addons/community/xform/value/";
$REX['ADDON']['community']['xform_path']['action'][] = $REX["INCLUDE_PATH"]."/addons/community/xform/action/";

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