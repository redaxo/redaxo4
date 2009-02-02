<?php

/**
 *
 * @package redaxo4
 * @version $Id: index.php,v 1.10 2008/04/02 18:12:39 kills Exp $
 */

// ----- caching start für output filter
ob_start();
ob_implicit_flush(0);

// ----------------- MAGIC QUOTES CHECK && REGISTER GLOBALS
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

// ----- header einbauen
$withheader = true;

// ----- pages, verfŸgbare seiten
// array(name,addon=1,htmlheader=1);
$pages = array();
$page = "";

// ----------------- SETUP
unset($REX_USER);
if ($REX['SETUP'])
{
  // ----------------- SET SETUP LANG
  $LOGIN = FALSE;
  $REX['LANG'] = 'de_de';
  $I18N = rex_create_lang($REX['LANG']);
  $requestLang = rex_request('lang', 'string');
  foreach ($REX['LOCALES'] as $l) {
    if ($requestLang == $l)
    {
      $REX['LANG'] = $l;
      $I18N = rex_create_lang($REX['LANG']);
      break;
    }
  }

  header('Content-Type: text/html; charset='.$I18N->msg('htmlcharset'));
  header('Cache-Control: no-cache');
  header('Pragma: no-cache');

  $pages["setup"] = array($I18N->msg('setup'),0,1);
  $page = "setup";
}
else
{
  // ----------------- CREATE LANG OBJ
  $I18N = rex_create_lang($REX['LANG']);
  header('Content-Type: text/html; charset='.$I18N->msg('htmlcharset'));
  header('Cache-Control: no-cache');
  header('Pragma: no-cache');

  // ---- prepare login
  $REX_LOGIN = new rex_backend_login($REX['TABLE_PREFIX'] .'user');
  $rex_user_login = rex_post('rex_user_login', 'string');
  $rex_user_psw = rex_post('rex_user_psw', 'string');

  if ($REX['PSWFUNC'] != '')
    $REX_LOGIN->setPasswordFunction($REX['PSWFUNC']);

  if (rex_get('rex_logout', 'boolean'))
    $REX_LOGIN->setLogout(true);

  $REX_LOGIN->setLogin($rex_user_login, $rex_user_psw);
  $loginCheck = $REX_LOGIN->checkLogin();

	$rex_user_loginmessage = "";
  if ($loginCheck !== true)
  {
  	// login failed

    $rex_user_loginmessage = $REX_LOGIN->message;

    // Fehlermeldung von der Datenbank
    if(is_string($loginCheck))
      $rex_user_loginmessage = $loginCheck;

    $LOGIN = FALSE;
    $pages["login"] = array("login",0,1);
    $page = 'login';
  } else
  {
  	
		// Userspezifische Sprache einstellen, falls gleicher Zeichensatz
  	$lang = $REX_LOGIN->getLanguage();
  	$I18N_T = rex_create_lang($lang,FALSE);
  	if ($I18N->msg('htmlcharset') == $I18N_T->msg('htmlcharset')) $I18N = rex_create_lang($lang);

    $LOGIN = TRUE;
    $REX_USER = $REX_LOGIN->USER;

		$pages["profile"] = array($I18N->msg("profile"),0,1);
		$pages["credits"] = array($I18N->msg("credits"),0,1);
		
		if ($REX_USER->isAdmin() || ($REX_USER->hasPerm('clang[') AND ($REX_USER->hasPerm('csw[') || $REX_USER->hasPerm('csr['))))
		{
			$pages["structure"] = array($I18N->msg("structure"),0,1);
			$pages["mediapool"] = array($I18N->msg("mediapool"),0,0);
			$pages["linkmap"] = array($I18N->msg("linkmap"),0,0);
			$pages["content"] = array($I18N->msg("content"),0,1);
		}elseif($REX_USER->hasPerm('mediapool[]')) 
		{
			$pages["mediapool"] = array($I18N->msg("mediapool"),0,0);
		}
		
		if ($REX_USER->hasPerm('template[]') || $REX_USER->isAdmin())
			$pages["template"] = array($I18N->msg("template"),0,1);
		
		if ($REX_USER->hasPerm('module[]') || $REX_USER->isAdmin())
			$pages["module"] = array($I18N->msg("modules"),0,1);
		
		if ($REX_USER->hasPerm('user[]') || $REX_USER->isAdmin())
			$pages["user"] = array($I18N->msg("user"),0,1);
		
		if ($REX_USER->hasPerm('addon[]') || $REX_USER->isAdmin())
			$pages["addon"] = array($I18N->msg("addon"),0,1);
		
		if ($REX_USER->hasPerm('specials[]') || $REX_USER->isAdmin()) 
			$pages["specials"] = array($I18N->msg("specials"),0,1);
		
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
	  			$link = '<a href="'.$link.'">';
				else 
					$link = '<a href="index.php?page='.$apage.'">';
				if (current($REX['ADDON']['status']) == 1 && $name != '' && ($perm == '' || $REX_USER->hasPerm($perm) || $REX_USER->isAdmin()))
				{
					$popup = 1;
      		if(isset ($REX['ADDON']['popup'][$apage]))
      			$popup = 0;
					$pages[$apage] = array($name,1,$popup,$link);
				}
				next($REX['ADDON']['status']);
			}
		}

		$REX_USER->pages = $pages;

		// --- page herausfinden
    $page = trim(strtolower(rex_request('page', 'string')));
    if($rex_user_login != "") $page = $REX_LOGIN->getStartpage();
    if(!isset($pages[$page]))
    {
    	$page = $REX_LOGIN->getStartpage();
	    if(!isset($pages[$page]))
	    {
	    	$page = $REX['START_PAGE'];
		    if(!isset($pages[$page]))
		    {
		    	$page = "profile";
		    }
	    }
    }
  	
    // --- login ok -> redirect
    if ($rex_user_login != "")
    {
      header('Location: index.php?page='. $page);
  		exit;
    }
  
  }
}

// Ausgabe der Seite
// $pages[$page][0] -> Name der Seite
// $pages[$page][1] -> Addon = 1
// $pages[$page][2] -> Headers = 1

$_REQUEST["page"] = $page;
$REX["pages"] = $pages;
$REX["page"] = $page; 

$REX["page_no_navi"] = 1;
if($pages[$page][2] == 1) $REX["page_no_navi"] = 0;

if($pages[$page][1])
{
	require $REX['INCLUDE_PATH'].'/addons/'. $page .'/pages/index.inc.php';
}else
{
  require $REX['INCLUDE_PATH'].'/layout/top.php';
	require $REX['INCLUDE_PATH'].'/pages/'. $page .'.inc.php';
  require $REX['INCLUDE_PATH'].'/layout/bottom.php';
}

// ----- caching end für output filter
$CONTENT = ob_get_contents();
ob_end_clean();

// ----- inhalt ausgeben
rex_send_article(null, $CONTENT, 'backend');