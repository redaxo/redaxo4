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

  $page_name = $I18N->msg('setup');
  $page = 'setup';
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
  $REX_ULOGIN = rex_post('REX_ULOGIN', 'string');
  $REX_UPSW = rex_post('REX_UPSW', 'string');

  if ($REX['PSWFUNC'] != '')
    $REX_LOGIN->setPasswordFunction($REX['PSWFUNC']);

  if (isset($FORM['logout']) and $FORM['logout'] == 1)
    $REX_LOGIN->setLogout(true);

  $REX_LOGIN->setLogin($REX_ULOGIN, $REX_UPSW);
  $loginCheck = $REX_LOGIN->checkLogin();

  if ($loginCheck !== true)
  {
  	// login failed

    $FORM['loginmessage'] = $REX_LOGIN->message;

    // Fehlermeldung von der Datenbank
    if(is_string($loginCheck))
      $FORM['loginmessage'] = $loginCheck;

    $LOGIN = FALSE;
    $page = 'login';
  } else
  {
  	
  	// --- page herausfinden
    $page = strtolower(rex_request('page', 'string'));
		if($page == "")
	 	{
	  	$page = $REX_LOGIN->getStartpage();
	  	if($page == "" || $page == "default") $page = $REX['START_PAGE'];
    }
  	
    // --- login ok -> redirect
    if ($REX_ULOGIN != "")
    {
      header('Location: index.php?page='. $page);
  		exit;
    }

		// Userspezifische Sprache einstellen, falls gleicher Zeichensatz
  	$lang = $REX_LOGIN->getLanguage();
  	$I18N_T = rex_create_lang($lang,FALSE);
  	if ($I18N->msg('htmlcharset') == $I18N_T->msg('htmlcharset')) $I18N = rex_create_lang($lang);

    $LOGIN = TRUE;
    $REX_USER = $REX_LOGIN->USER;

    // --- addon page check
    if (isset($REX['ADDON']['page']) && is_array($REX['ADDON']['page']))
    {
      include_once $REX['INCLUDE_PATH'].'/functions/function_rex_addons.inc.php';

      $as = rex_search_addon_page($page);
      if ($as !== false)
      {
        // --- addon gefunden
        $perm = $REX['ADDON']['perm'][$as];
        $hasPerm = $perm == '' || $REX_USER->hasPerm($perm) || $REX_USER->hasPerm('admin[]');

        // Suche zuerst nach einem Addon, dass so heisst wie die aktuelle page
        // z.b addons/structure/pages/index.inc.php
        $addon_page = $REX['INCLUDE_PATH'].'/addons/'. $page .'/pages/index.inc.php';
        if(file_exists($addon_page) && $hasPerm && OOAddon::isAvailable($page))
        {
          $withheader = false;
          $REX['PAGEPATH'] = $addon_page;

          if(isset($REX['ADDON']['name'][$page]))
            $page_name = rex_translate($REX['ADDON']['name'][$page]);
        }
        else
        {
          // Kein Addon gefunden, also suchen wir nach einem Addon,
          // dass vorgegeben hat, eine Page zu haben, die so heisst, wie die aktuelle
          // z.b addons/xxx/pages/structure.inc.php
          $addon_page = $REX['INCLUDE_PATH'].'/addons/'. $as .'/pages/'. $page .'.inc.php';
          if(file_exists($addon_page) && $hasPerm && OOAddon::isAvailable($as))
          {
            $withheader = false;
            $REX['PAGEPATH'] = $addon_page;

            if(isset($REX['ADDON']['name'][$page]))
              $page_name = rex_translate($REX['ADDON']['name'][$page]);
          }
        }
      }
    }

    // ----- standard pages
    if ($REX['PAGEPATH'] == '' && $page == 'addon' && ($REX_USER->hasPerm('addon[]') || $REX_USER->hasPerm('admin[]')))
    {
      $page_name = $I18N->msg('addon');
    }elseif ($REX['PAGEPATH'] == '' && $page == 'specials' && ($REX_USER->hasPerm('specials[]') || $REX_USER->hasPerm('admin[]')))
    {
      $page_name = $I18N->msg('specials');
    }elseif ($REX['PAGEPATH'] == '' && $page == 'module' && ($REX_USER->hasPerm('module[]') || $REX_USER->hasPerm('admin[]')))
    {
      $page_name = $I18N->msg('modules');
    }elseif ($REX['PAGEPATH'] == '' && $page == 'template' && ($REX_USER->hasPerm('template[]') || $REX_USER->hasPerm('admin[]')))
    {
      $page_name = $I18N->msg('template');
    }elseif ($REX['PAGEPATH'] == '' && $page == 'user' && ($REX_USER->hasPerm('user[]') || $REX_USER->hasPerm('admin[]')))
    {
      $page_name = $I18N->msg('user');
    }elseif ($REX['PAGEPATH'] == '' && $page == 'medienpool')
    {
      $page_name = $I18N->msg('pool_media');
      $open_header_only = true;
    }elseif ($REX['PAGEPATH'] == '' && $page == 'linkmap')
    {
      $page_name = $I18N->msg('lmap_title');
      $open_header_only = true;
    }elseif ($REX['PAGEPATH'] == '' && $page == 'content')
    {
      $page_name = $I18N->msg('content');
    }elseif ($REX['PAGEPATH'] == '' && $page == 'credits')
    {
      $page_name = $I18N->msg('credits');
    }elseif ($REX['PAGEPATH'] == '' && $page == 'profile')
    {
      $page_name = $I18N->msg('profile');
    }elseif($REX['PAGEPATH'] == '')
    {
      $page = 'structure';
      $page_name = $I18N->msg('structure');
    }
  }
}

// ----- kein pagepath -> kein addon -> path setzen
if ($REX['PAGEPATH'] == '') $REX['PAGEPATH'] = $REX['INCLUDE_PATH'].'/pages/'. $page .'.inc.php';

// ----- ausgabe des includes
if ($withheader) require $REX['INCLUDE_PATH'].'/layout/top.php';
require $REX['PAGEPATH'];
if ($withheader) require $REX['INCLUDE_PATH'].'/layout/bottom.php';

// ----- caching end für output filter
$CONTENT = ob_get_contents();
ob_end_clean();

// ----- inhalt ausgeben
rex_send_article(null, $CONTENT, 'backend');