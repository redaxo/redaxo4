<?php

error_reporting(E_ALL);

/** 
 *  
 * @package redaxo3
 * @version $Id$
 */ 

// ----- caching start für output filter

ob_start();
ob_implicit_flush(0);

// ----------------- MAGIC QUOTES CHECK && REGISTER GLOBALS
include './include/functions/function_rex_mquotes.inc.php';

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
include 'include/master.inc.php';

// ----- addon/normal page path
$REX['PAGEPATH'] = '';

// ----- header einbauen
$withheader = true;

// ----------------- SETUP
if ($REX['SETUP'])
{
  // ----------------- SET SETUP LANG
  $LOGIN = FALSE;
  $REX['LANG'] = 'en_gb';
  $I18N = rex_create_lang($REX['LANG']);
  foreach ($REX['LOCALES'] as $l) {
    if (isset($_REQUEST['lang']) && $_REQUEST['lang'] == $l) 
    {
      $REX['LANG'] = $l;
      $I18N = rex_create_lang($REX['LANG']);
    }
  }

  setlocale(LC_ALL,trim($I18N->msg('setlocale')));
  header('Content-Type: text/html; charset='.$I18N->msg('htmlcharset'));
  
  $page_name = $I18N->msg('setup');
  $page = 'setup';
}
else
{

  // ----------------- CREATE LANG OBJ
  $I18N = rex_create_lang($REX['LANG']);
  $locale = trim($I18N->msg('setlocale'));
  $charset = trim($I18N->msg('htmlcharset'));
  $charset_alt = str_replace('iso-','iso',$charset);
  setlocale(LC_ALL,
  	$locale.'.'.$charset,
  	$locale.'.'.$charset_alt,
		$locale);
  header('Content-Type: text/html; charset='.$I18N->msg('htmlcharset'));
  header('Cache-Control: no-cache');
  header('Pragma: no-cache');

  // ----------------- CREATE LANG OBJ
  if (!isset($REX_ULOGIN))
    $REX_ULOGIN = '';
  if (!isset($REX_UPSW))
    $REX_UPSW = '';
  
  $REX_LOGIN = new rex_login();
  $REX_LOGIN->setSqlDb(1);
  $REX_LOGIN->setSysID($REX['INSTNAME']);
  $REX_LOGIN->setSessiontime(3000);
  
  if ($REX['PSWFUNC'] != '')
    $REX_LOGIN->setPasswordFunction($REX['PSWFUNC']);
    
  if (isset($FORM['logout']) and $FORM['logout'] == 1)
    $REX_LOGIN->setLogout(true);
    
  $REX_LOGIN->setLogin($REX_ULOGIN, $REX_UPSW);
  $REX_LOGIN->setUserID($REX['TABLE_PREFIX'].'user.user_id');
  $REX_LOGIN->setUserquery('SELECT * FROM '.$REX['TABLE_PREFIX'].'user WHERE status=1 AND user_id = "USR_UID"');
  $REX_LOGIN->setLoginquery('SELECT * FROM '.$REX['TABLE_PREFIX'].'user WHERE status=1 AND login = "USR_LOGIN" AND psw = "USR_PSW" AND lasttrydate <'. (time()-$REX['RELOGINDELAY']).' AND login_tries<'.$REX['MAXLOGINS']);
  
  if (!$REX_LOGIN->checkLogin())
  {
  	// login failed
    $FORM['loginmessage'] = $REX_LOGIN->message;
    $LOGIN = FALSE;
    $page = 'login';
    
    // fehlversuch speichern | login_tries++
    if ($REX_ULOGIN != '')
    {
        $fvs = new rex_sql;
        $fvs->setQuery('UPDATE '.$REX['TABLE_PREFIX'].'user SET login_tries=login_tries+1,lasttrydate='.time().' WHERE login="'. $REX_ULOGIN .'"');
    }
    
  } else
  {

  	// gelungenen versuch speichern | login_tries = 0
    if ($REX_ULOGIN != "")
    {
    	// ----- session fixation
			$tmp = $_SESSION;
			/*
			var_dump($tmp);
			echo "<br /";
			var_dump($_SESSION);
			echo "<br /";
			*/
      session_unset();
      // session_destroy();
      session_regenerate_id(true);
      $_SESSION = $tmp;
			/*
			var_dump($tmp);
			echo "<br /";
			var_dump($_SESSION);
			echo "<br /";
		  exit;
		  */
      $fvs = new rex_sql;
      $fvs->setQuery('UPDATE '.$REX['TABLE_PREFIX'].'user SET login_tries=0, lasttrydate='.time().' WHERE login="'. $REX_ULOGIN .'"');
  		header('Location: index.php?page='. $REX['START_PAGE']);
  		exit;
    }
    	
  	// login ok 
    $LOGIN = TRUE;
    $REX_USER = $REX_LOGIN->USER;
  
    if (isset($page)) { 
      $page = strtolower($page); 
    } else {
      $page = '';
    }
    
    // --- addon page check
    if (isset($REX['ADDON']['page']) && is_array($REX['ADDON']['page']))
    {
      $as = array_search($page,$REX['ADDON']['page']);
      if ($as !== false)
      {
        // --- addon gefunden 
        $perm = $REX['ADDON']['perm'][$as];
        if($REX['ADDON']['status'][$page] == 1 && ($REX_USER->isValueOf('rights',$perm) or $perm == '' or $REX_USER->isValueOf('rights','admin[]')))
        {
          $withheader = false;
          $REX['PAGEPATH'] = $REX['INCLUDE_PATH'].'/addons/'. $page .'/pages/index.inc.php';
        }
      }
    }
    
    // ----- standard pages    
    if ($REX['PAGEPATH'] == '' && $page == 'addon' && ($REX_USER->isValueOf('rights','addon[]') || $REX_USER->isValueOf('rights','admin[]')))
    {
      $page_name = $I18N->msg('addon');
    }elseif ($REX['PAGEPATH'] == '' && $page == 'specials' && ($REX_USER->isValueOf('rights','specials[]') || $REX_USER->isValueOf('rights','admin[]')))
    {
      $page_name = $I18N->msg('specials');
    }elseif ($REX['PAGEPATH'] == '' && $page == 'module' && ($REX_USER->isValueOf('rights','module[]') || $REX_USER->isValueOf('rights','admin[]')))
    {
      $page_name = $I18N->msg('modules');
    }elseif ($REX['PAGEPATH'] == '' && $page == 'template' && ($REX_USER->isValueOf('rights','template[]') || $REX_USER->isValueOf('rights','admin[]')))
    {
      $page_name = $I18N->msg('template');
    }elseif ($REX['PAGEPATH'] == '' && $page == 'user' && ($REX_USER->isValueOf('rights','user[]') || $REX_USER->isValueOf('rights','admin[]')))
    {
      $page_name = $I18N->msg('user');
    }elseif ($REX['PAGEPATH'] == '' && $page == 'medienpool')
    {
      $page_name = $I18N->msg('pool_media');
      $open_header_only = true;
    }elseif ($REX['PAGEPATH'] == '' && $page == 'linkmap')
    {
      $open_header_only = true;
    }elseif ($REX['PAGEPATH'] == '' && $page == 'content')
    {
      $page_name = $I18N->msg('content');
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
if ($withheader) include $REX['INCLUDE_PATH'].'/layout/top.php';
include $REX['PAGEPATH'];
if ($withheader) include $REX['INCLUDE_PATH'].'/layout/bottom.php';

// ----- caching end für output filter
$CONTENT = ob_get_contents();
ob_end_clean();

// ----- EXTENSION POINT
$CONTENT = rex_register_extension_point( 'OUTPUT_FILTER', $CONTENT);

// ----- EXTENSION POINT - keine Manipulation der Ausgaben ab hier (read only)
rex_register_extension_point( 'OUTPUT_FILTER_CACHE', $CONTENT, '', true);

// ----- inhalt endgueltig ausgeben
echo $CONTENT;

// DB Verbindung schließen
$sql = rex_sql::getInstance();
$sql->disconnect();

?>