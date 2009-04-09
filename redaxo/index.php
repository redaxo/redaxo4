<?php

/**
 *
 * @package redaxo4
 * @version svn:$Id$
 */

// ----- caching start für output filter
ob_start();
ob_implicit_flush(0);

// ----------------- MAGIC QUOTES CHECK
require './include/functions/function_rex_mquotes.inc.php';

// ----- REX UNSET
unset($REX);

// Flag ob Inhalte mit Redaxo aufgerufen oder
// von der Webseite aus
// Kann wichtig für die Darstellung sein
// Sollte immer true bleiben

$REX['REDAXO'] = true;

// Wenn $REX[GG] = true; dann wird der
// Content aus den redaxo/include/generated/
// genommen

$REX['GG'] = false;

// setzte pfad und includiere klassen und funktionen
$REX['HTDOCS_PATH'] = '../';
require 'include/master.inc.php';

// ----- addon/normal page path
$REX['PAGEPATH'] = '';

// ----- pages, verfuegbare seiten
// array(name,addon=1,htmlheader=1);
$REX['PAGES'] = array();
$REX['PAGE'] = '';

// ----------------- SETUP
$REX['USER'] = NULL;
$REX['LOGIN'] = NULL;

if ($REX['SETUP'])
{
	// ----------------- SET SETUP LANG
	$REX['LANG'] = '';
	$requestLang = rex_request('lang', 'string');
	$langpath = $REX['INCLUDE_PATH'].'/lang';
	$REX['LANGUAGES'] = array();
	if ($handle = opendir($langpath))
	{
		while (false !== ($file = readdir($handle)))
		{
			if (substr($file,-5) == '.lang')
			{
				$locale = substr($file,0,strlen($file)-strlen(substr($file,-5)));
				$REX['LANGUAGES'][] = $locale;
				if($requestLang == $locale)
					$REX['LANG'] = $locale;
			}
		}
	}
	closedir($handle);
	if($REX['LANG'] == '')
		$REX['LANG'] = 'de_de';

  $I18N = rex_create_lang($REX['LANG']);
	
	$REX['PAGES']["setup"] = array($I18N->msg('setup'),0,1);
	$REX['PAGE'] = "setup";

}else
{
	// ----------------- CREATE LANG OBJ
	$I18N = rex_create_lang($REX['LANG']);

	// ---- prepare login
	$REX['LOGIN'] = new rex_backend_login($REX['TABLE_PREFIX'] .'user');
	$rex_user_login = rex_post('rex_user_login', 'string');
	$rex_user_psw = rex_post('rex_user_psw', 'string');

	if ($REX['PSWFUNC'] != '')
	  $REX['LOGIN']->setPasswordFunction($REX['PSWFUNC']);

	if (rex_get('rex_logout', 'boolean'))
	  $REX['LOGIN']->setLogout(true);

	$REX['LOGIN']->setLogin($rex_user_login, $rex_user_psw);
	$loginCheck = $REX['LOGIN']->checkLogin();

	$rex_user_loginmessage = "";
	if ($loginCheck !== true)
	{
		// login failed
		$rex_user_loginmessage = $REX['LOGIN']->message;

		// Fehlermeldung von der Datenbank
		if(is_string($loginCheck))
		  $rex_user_loginmessage = $loginCheck;

		$REX['PAGES']["login"] = array("login",0,1);
		$REX['PAGE'] = 'login';
		
		$REX['USER'] = NULL;
		$REX['LOGIN'] = NULL;
	}
	else
	{		 
		// Userspezifische Sprache einstellen, falls gleicher Zeichensatz
		$lang = $REX['LOGIN']->getLanguage();
		$I18N_T = rex_create_lang($lang,'',FALSE);
		if ($I18N->msg('htmlcharset') == $I18N_T->msg('htmlcharset')) 
			$I18N = rex_create_lang($lang);

		$REX['USER'] = $REX['LOGIN']->USER;
	}
}

// ----- Prepare Core Pages
if($REX['USER'])
{
	$REX['PAGES']["profile"] = array($I18N->msg("profile"),0,1);
	$REX['PAGES']["credits"] = array($I18N->msg("credits"),0,1);

	if ($REX['USER']->isAdmin() || $REX['USER']->hasStructurePerm())
	{
		$REX['PAGES']["structure"] = array($I18N->msg("structure"),0,1);
		$REX['PAGES']["mediapool"] = array($I18N->msg("mediapool"),0,0,'NAVI' => array('href' =>'#', 'onclick' => 'openMediaPool()', 'class' => ' rex-popup'));
		$REX['PAGES']["linkmap"] = array($I18N->msg("linkmap"),0,0);
		$REX['PAGES']["content"] = array($I18N->msg("content"),0,1);
	}elseif($REX['USER']->hasPerm('mediapool[]'))
	{
		$REX['PAGES']["mediapool"] = array($I18N->msg("mediapool"),0,0,'NAVI' => array('href' =>'#', 'onclick' => 'openMediaPool()', 'class' => ' rex-popup'));
	}

	if ($REX['USER']->isAdmin())
	{
	  $REX['PAGES']["template"] = array($I18N->msg("template"),0,1);
	  $REX['PAGES']["module"] = array($I18N->msg("modules"),0,1,'SUBPAGES'=>array(array('',$I18N->msg("modules")),array('actions',$I18N->msg("actions"))));
	  $REX['PAGES']["user"] = array($I18N->msg("user"),0,1);
	  $REX['PAGES']["addon"] = array($I18N->msg("addon"),0,1);
	  $REX['PAGES']["specials"] = array($I18N->msg("specials"),0,1,'SUBPAGES'=>array(array('',$I18N->msg("main_preferences")),array('lang',$I18N->msg("languages"))));
	}
}

// ----- INCLUDE ADDONS
include_once $REX['INCLUDE_PATH'].'/addons.inc.php';

// ----- Prepare AddOn Pages
if($REX['USER'])
{
	if (is_array($REX['ADDON']['status']))
	  reset($REX['ADDON']['status']);

	$onlineAddons = array_filter(array_values($REX['ADDON']['status']));
	if(count($onlineAddons) > 0)
	{
		for ($i = 0; $i < count($REX['ADDON']['status']); $i++)
		{
			$apage = key($REX['ADDON']['status']);
			
			$perm = '';
			if(isset ($REX['ADDON']['perm'][$apage]))
			  $perm = $REX['ADDON']['perm'][$apage];
			  
			$name = '';
			if(isset ($REX['ADDON']['name'][$apage]))
			  $name = $REX['ADDON']['name'][$apage];
			  
			if(isset ($REX['ADDON']['link'][$apage]) && $REX['ADDON']['link'][$apage] != "")
			  $link = '<a href="'.$REX['ADDON']['link'][$apage].'">';
			else
			  $link = '<a href="index.php?page='.$apage.'">';
			  
			if (current($REX['ADDON']['status']) == 1 && $name != '' && ($perm == '' || $REX['USER']->hasPerm($perm) || $REX['USER']->isAdmin()))
			{
				$popup = 1;
				if(isset ($REX['ADDON']['popup'][$apage]))
				  $popup = 0;
				  
				$REX['PAGES'][strtolower($apage)] = array($name,1,$popup,$link);
			}
			next($REX['ADDON']['status']);
		}
	}
}

// Set Startpage
if($REX['USER'])
{
	$REX['USER']->pages = $REX['PAGES'];

	// --- page herausfinden
	$REX['PAGE'] = trim(strtolower(rex_request('page', 'string')));
	if($rex_user_login != "") 
		$REX['PAGE'] = $REX['LOGIN']->getStartpage();
	if(!isset($REX['PAGES'][strtolower($REX['PAGE'])]))
	{
		$REX['PAGE'] = $REX['LOGIN']->getStartpage();
		if(!isset($REX['PAGES'][strtolower($REX['PAGE'])]))
		{
			$REX['PAGE'] = $REX['START_PAGE'];
			if(!isset($REX['PAGES'][strtolower($REX['PAGE'])]))
			{
				$REX['PAGE'] = "profile";
			}
		}
	}
	 
	// --- login ok -> redirect
	if ($rex_user_login != "")
	{
		header('Location: index.php?page='. $REX['PAGE']);
		exit();
	}
}

$REX["PAGE_NO_NAVI"] = 1;
if($REX['PAGES'][strtolower($REX['PAGE'])][2] == 1) 
	$REX["PAGE_NO_NAVI"] = 0;

// ----- EXTENSION POINT
// page variable validated
rex_register_extension_point( 'PAGE_CHECKED', $REX['PAGE'], array('pages' => $REX['PAGES']));


if(isset($REX['PAGES'][$REX['PAGE']]['PATH']) && $REX['PAGES'][$REX['PAGE']]['PATH'] != "")
{
	// If page has a new/overwritten path
	require $REX['PAGES'][$REX['PAGE']]['PATH'];

}elseif($REX['PAGES'][strtolower($REX['PAGE'])][1])
{
  // Addon Page
  require $REX['INCLUDE_PATH'].'/addons/'. $REX['PAGE'] .'/pages/index.inc.php';
	
}else
{
	// Core Page
	require $REX['INCLUDE_PATH'].'/layout/top.php';
	require $REX['INCLUDE_PATH'].'/pages/'. $REX['PAGE'] .'.inc.php';
	require $REX['INCLUDE_PATH'].'/layout/bottom.php';
}
// ----- caching end für output filter
$CONTENT = ob_get_contents();
ob_end_clean();

// ----- inhalt ausgeben
rex_send_article(null, $CONTENT, 'backend', TRUE);