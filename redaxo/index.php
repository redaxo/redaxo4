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
	
	$REX['PAGES']["setup"] = array('title'=>$I18N->msg('setup'), 'hide_navi'=>TRUE, 'type'=>'system');
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

		$REX['PAGES']["login"] = array('title'=>"login", 'hide_navi'=>TRUE, 'type'=>'system');
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
	$REX['PAGES']['profile'] = array('title'=>$I18N->msg('profile'), 'type'=>'system');
	$REX['PAGES']['credits'] = array('title'=>$I18N->msg('credits'), 'type'=>'system');

	if ($REX['USER']->isAdmin() || $REX['USER']->hasStructurePerm())
	{
		$REX['PAGES']['structure'] = array('title'=>$I18N->msg('structure'), 'type'=>'system', 'active_when' => array('page'=>'structure'));
		if($REX['USER']->hasMediaPerm())
			$REX['PAGES']['mediapool'] = array('title'=>$I18N->msg('mediapool'), 'hide_navi'=>TRUE, 'href' =>'#', 'onclick' => 'openMediaPool()', 'class' => 'rex-popup', 'type'=>'system');
		$REX['PAGES']['linkmap'] = array('title'=>$I18N->msg('linkmap'), 'hide_navi'=>TRUE, 'type'=>'system');
		$REX['PAGES']['content'] = array('title'=>$I18N->msg('content'), 'type'=>'system');
	}elseif($REX['USER']->hasMediaPerm())
	{
		$REX['PAGES']['mediapool'] = array('title'=>$I18N->msg('mediapool'), 'hide_navi'=>TRUE, 'href' =>'#', 'onclick' => 'openMediaPool()', 'class' => 'rex-popup', 'type'=>'system');
	}

	if ($REX['USER']->isAdmin())
	{
	  $REX['PAGES']['template'] = array('title'=>$I18N->msg('template'), 'type'=>'system', 'active_when' => array('page'=>'template'));
	  $REX['PAGES']['module'] = array('title'=>$I18N->msg('modules'), 'type'=>'system', 'active_when' => array('page'=>'module'),
		  'subpages'=>
		    array(
		  	  array('page' => '', 'title'=>$I18N->msg('modules'), 'href'=>'index.php?page=module&subpage=', 'active_when' => array('page'=>'module', 'subpage' => '')),
		  		array('page' => 'actions', 'title'=>$I18N->msg('actions'), 'href'=>'index.php?page=module&subpage=actions', 'active_when' => array('page'=>'module', 'subpage' => 'actions'))
		  	)
		);
	  $REX['PAGES']['user'] = array('title'=>$I18N->msg('user'), 'type'=>'system', 'active_when' => array('page'=>'user'));
	  $REX['PAGES']['addon'] = array('title'=>$I18N->msg('addon'), 'type'=>'system', 'active_when' => array('page'=>'addon'));
	  $REX['PAGES']['specials'] = array('title'=>$I18N->msg('specials'), 'type'=>'system', 'active_when' => array('page'=>'specials'),
	    'subpages'=>
	  		array(
	  			array('title'=>$I18N->msg('main_preferences'), 'href'=>'index.php?page=specials&subpage=', 'active_when' => array('page'=>'specials', 'subpage' => '')),
	  			array('title'=>$I18N->msg('languages'), 'href'=>'index.php?page=specials&subpage=lang', 'active_when' => array('page'=>'specials', 'subpage' => 'lang'))
	  		)
	  );
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
			$apage_arr = array();
			$apage_arr['type'] = 'addons';
			$apage_arr['title'] = '';
			
			if(isset ($REX['ADDON']['name'][$apage]))
			  $apage_arr['title'] = $REX['ADDON']['name'][$apage];
			  
			if(isset ($REX['ADDON']['link'][$apage]) && $REX['ADDON']['link'][$apage] != '')
			  $apage_arr['href'] = $REX['ADDON']['link'][$apage];
			else
			  $apage_arr['href'] = 'index.php?page='.$apage;
			
			$apage_arr['active_when'] = array('page' => $apage);
			  
			$perm = '';
			if(isset ($REX['ADDON']['perm'][$apage]))
			  $perm = $REX['ADDON']['perm'][$apage];
			  
			if (current($REX['ADDON']['status']) == 1 && $apage_arr['title'] != '' && ($perm == '' || $REX['USER']->hasPerm($perm) || $REX['USER']->isAdmin()))
			{
			  // wegen REX Version < 4.2 - alter stil "SUBPAGES", neuer "subpages" mit active_when
				if(isset($REX['ADDON'][$apage]['SUBPAGES']))
				{
				  $REX['ADDON'][$apage]['subpages'] = $REX['ADDON'][$apage]['SUBPAGES'];
				}
				if(isset($REX['ADDON'][$apage]['subpages']))
				{
				  $sp = array();
				  foreach($REX['ADDON'][$apage]['subpages'] as $s)
				  {
				 	 if(isset($REX['ADDON'][$apage]['SUBPAGES']))
				 	 {
				 	   $sp[] = array(
				 	   	'title'=>$s[1], 
				 	   	'active_when' => array('page' => $apage, 'subpage' => $s[0]), 
				 	   	'href'=>'index.php?page='.$apage.'&subpage='.$s[0]);
				 	 }
				  }
				  $apage_arr['subpages'] = $sp;
				 }
				 // *** ENDE wegen <4.2
				 
				if(isset($REX['ADDON']['navigation'][$apage]))
				  $apage_arr = array_merge($apage_arr,$REX['ADDON']['navigation'][$apage]);
				$REX['PAGES'][$apage] = $apage_arr;
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
	$REX['PAGE'] = trim(rex_request('page', 'string'));
	if($rex_user_login != '') 
		$REX['PAGE'] = $REX['LOGIN']->getStartpage();
	if(!isset($REX['PAGES'][$REX['PAGE']]))
	{
		$REX['PAGE'] = $REX['LOGIN']->getStartpage();
		if(!isset($REX['PAGES'][$REX['PAGE']]))
		{
			$REX['PAGE'] = $REX['START_PAGE'];
			if(!isset($REX['PAGES'][$REX['PAGE']]))
			{
				$REX['PAGE'] = 'profile';
			}
		}
	}
	 
	// --- login ok -> redirect
	if ($rex_user_login != '')
	{
		header('Location: index.php?page='. $REX['PAGE']);
		exit();
	}
}

$REX['PAGE_NO_NAVI'] = FALSE;
if(isset($REX['PAGES'][$REX['PAGE']]['hide_navi']) && $REX['PAGES'][$REX['PAGE']]['hide_navi']) 
	$REX['PAGE_NO_NAVI'] = TRUE;


// ----- EXTENSION POINT
// page variable validated
rex_register_extension_point( 'PAGE_CHECKED', $REX['PAGE'], array('pages' => $REX['PAGES']));


if(isset($REX['PAGES'][$REX['PAGE']]['PATH']) && $REX['PAGES'][$REX['PAGE']]['PATH'] != '')
{
	// If page has a new/overwritten path
	require $REX['PAGES'][$REX['PAGE']]['PATH'];

}elseif($REX['PAGES'][$REX['PAGE']]['type'] == 'system')
{
	// Core Page
	require $REX['INCLUDE_PATH'].'/layout/top.php';
	require $REX['INCLUDE_PATH'].'/pages/'. $REX['PAGE'] .'.inc.php';
	require $REX['INCLUDE_PATH'].'/layout/bottom.php';
}else
{
	// Addon Page
  require $REX['INCLUDE_PATH'].'/addons/'. $REX['PAGE'] .'/pages/index.inc.php';
}
// ----- caching end für output filter
$CONTENT = ob_get_contents();
ob_end_clean();

// ----- inhalt ausgeben
rex_send_resource($CONTENT);