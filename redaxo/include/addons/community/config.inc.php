<?php

// error_reporting(E_ALL ^ E_NOTICE);

$mypage = "community";        // only for this file

$REX['ADDON']['page'][$mypage] = "$mypage";     // pagename/foldername
$REX['ADDON']['name'][$mypage] = "Community";   // name
$REX['ADDON']['perm'][$mypage] = "community[]"; // benoetigt mindest permission
$REX['ADDON']['version'][$mypage] = '1.0 rc3';
$REX['ADDON']['author'][$mypage] = 'Jan Kristinus, Markus Staab';
$REX['ADDON']['supportpage'][$mypage] = 'community.redaxo.de';
$REX['PERM'][] = "community[]";

$I18N_COM = new i18n($REX['LANG'], $REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/lang');

// ----- Community User Funktionen
include $REX["INCLUDE_PATH"]."/addons/community/functions/functions.rex_com_user.inc.php";
include $REX["INCLUDE_PATH"]."/addons/community/functions/functions.rex_com_replace.inc.php";
include $REX["INCLUDE_PATH"]."/addons/community/functions/functions.rex_com_blaettern.inc.php";
include $REX["INCLUDE_PATH"]."/addons/community/functions/functions.rex_com_formatter.inc.php";

if ($REX["REDAXO"])
{
	$REX['EXTRAPERM'][] = "community[admin]";
	$REX['EXTRAPERM'][] = "community[users]";
	include $REX["INCLUDE_PATH"]."/addons/community/functions/functions.userconfig.inc.php";
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

// ----- PlugIns
$REX['ADDON']['community']['subpages'] = array(); // Welche Seiten werden noch eingebunden
require $REX["INCLUDE_PATH"]."/addons/community/classes/class.ooplugin.inc.php";
require $REX["INCLUDE_PATH"]."/addons/community/plugins.inc.php";
foreach(OOPlugin::getAvailablePlugins() as $plugin_name)
{
	include $REX["INCLUDE_PATH"].'/addons/community/plugins/'.$plugin_name.'/config.inc.php';
}

/*
//	Tab Start
define("REX_COM_PAGE_PROFIL_ID",42);
define("REX_COM_PAGE_MYPROFIL_ID",30);
define("REX_COM_PAGE_REGISTER_ID",32);
define("REX_COM_PAGE_PSWFORGOTTEN_ID",33);

//	Tab Ende


// status wird über kategorie vergeben.
// in metaform eingetragen mit default = 1 und select/radiobox
// -> cat_perms
// --> 1 _ alle, 2 _ nur eingeloggte, 3 _ nur nicht eingeloggte, 4 _ admins

$REX["ADDON_COMMUNITY"]["config"]["link"]["login"]["id"] = 1;
$REX["ADDON_COMMUNITY"]["config"]["link"]["userdetail"]["id"] = 207;
// $REX["ADDON_COMMUNITY"]["config"]["link"]["sendmessage"]["id"] = 174;
$REX["ADDON_COMMUNITY"]["config"]["link"]["sendmessage"]["id"]["params"] = array("tab"=>2);
$REX["ADDON_COMMUNITY"]["config"]["link"]["userdetailedit"]["id"] = 186;

define("REX_COM_PAGE_PROFIL_ID",42);
define("REX_COM_PAGE_MYPROFIL_ID",14);
define("REX_COM_PAGE_REGISTER_ID",33);
define("REX_COM_PAGE_PSWFORGOTTEN_ID",32);

define("REX_COM_PAGE_LOGIN_ID",3);
define("REX_COM_PAGE_SENDMESSAGE_ID",6);

*/

// include $REX["INCLUDE_PATH"]."/addons/community/functions/functions.checkuserperm.inc.php";

?>