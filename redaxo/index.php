<?php

/** 
 *  
 * @package redaxo3
 * @version $Id$
 */ 

// ----- caching start für output filter

ob_start();

// ----- REX UNSET

unset($REX);

$REX['HTDOCS_PATH'] = "../";
$REX['GG'] = false;
$REX['REDAXO'] = true;

include "include/master.inc.php";

session_start();

// ----- addon/normal page path
$REX['PAGEPATH'] = "";

// ----- header einbauen
$withheader = true;

// ----------------- SETUP
if ($REX['SETUP'])
{
  // ----------------- SET SETUP LANG
  $LOGIN = FALSE;
  $REX['LANG'] = "en_gb";
  $I18N = rex_create_lang( $REX['LANG']);
  foreach ($REX['LOCALES'] as $l) {
    if (isset($_REQUEST["lang"]) && $_REQUEST["lang"] == $l) 
    {
      $REX['LANG'] = $l;
      $I18N = rex_create_lang( $REX['LANG']);
    }
  }

  setlocale(LC_ALL,trim($I18N->msg("setlocale")));
  header('Content-Type: text/html; charset='.$I18N->msg("htmlcharset"));
  
  $page_name = $I18N->msg("setup");
  $page = "setup";
  
} else
{

  // ----------------- CREATE LANG OBJ
  $I18N = rex_create_lang( $REX['LANG']);
  setlocale(LC_ALL,trim($I18N->msg("setlocale")));
  header('Content-Type: text/html; charset='.$I18N->msg("htmlcharset"));

  // ----------------- CREATE LANG OBJ
  if (!isset($REX_ULOGIN)) { $REX_ULOGIN = ''; }
  if (!isset($REX_UPSW)) { $REX_UPSW = ''; }
  $REX_LOGIN = new login();
  $REX_LOGIN->setSqlDb(1);
  $REX_LOGIN->setSysID($REX['INSTNAME']);
  $REX_LOGIN->setSessiontime(3000);
  if ($REX['PSWFUNC'] != "") $REX_LOGIN->setPasswordFunction($REX['PSWFUNC']);
  $REX_LOGIN->setLogin($REX_ULOGIN, $REX_UPSW);
  if (isset($FORM['logout']) and $FORM['logout'] == 1) $REX_LOGIN->setLogout(true);
  $REX_LOGIN->setUserID("rex_user.user_id");
  $REX_LOGIN->setUserquery("SELECT * FROM rex_user WHERE user_id = 'USR_UID'");
  $REX_LOGIN->setLoginquery("SELECT * FROM rex_user WHERE login = 'USR_LOGIN' and psw = 'USR_PSW'");

  if (!$REX_LOGIN->checkLogin())
  {
  	// login failed
    $FORM["loginmessage"]= $REX_LOGIN->message;
    $LOGIN = FALSE;
    $page = "login";
  } else
  {
  	// login ok 
    $LOGIN = TRUE;
    $REX_USER = $REX_LOGIN->USER;
  
    if (isset($page)) { 
      $page = strtolower($page); 
    } else {
      $page = '';
    }
    
    // --- addon page check
    $as = array_search($page,$REX['ADDON']['page']);
    if ($as !== false)
    {
      // --- addon gefunden 
      $perm = $REX['ADDON']['perm'][$as];
      if($REX_USER->isValueOf("rights",$perm) or $perm == "" or $REX_USER->isValueOf("rights","admin[]"))
      {
        $withheader = false;
        $REX['PAGEPATH'] = $REX['INCLUDE_PATH']."/addons/$page/pages/index.inc.php";
      }
    }
    
    // ----- standard pages    
    if ($REX['PAGEPATH'] == '' && $page == 'addon' && ($REX_USER->isValueOf("rights","addon[]") || $REX_USER->isValueOf("rights","admin[]")))
    {
      $page_name = $I18N->msg("addon");
    }elseif ($REX['PAGEPATH'] == '' && $page == "specials" && ($REX_USER->isValueOf("rights","specials[]") || $REX_USER->isValueOf("rights","admin[]")))
    {
      $page_name = $I18N->msg("specials");
    }elseif ($REX['PAGEPATH'] == '' && $page == "module" && ($REX_USER->isValueOf("rights","module[]") || $REX_USER->isValueOf("rights","admin[]")))
    {
      $page_name = $I18N->msg("module");
    }elseif ($REX['PAGEPATH'] == '' && $page == "template" && ($REX_USER->isValueOf("rights","template[]") || $REX_USER->isValueOf("rights","admin[]")))
    {
      $page_name = $I18N->msg("template");
    }elseif ($REX['PAGEPATH'] == '' && $page == "user" && ($REX_USER->isValueOf("rights","user[]") || $REX_USER->isValueOf("rights","admin[]")))
    {
      $page_name = $I18N->msg("user");
    }elseif ($REX['PAGEPATH'] == '' && $page == "medienpool")
    {
      $withheader = false;
    }elseif ($REX['PAGEPATH'] == '' && $page == "linkmap")
    {
      $open_header_only = true;
    }elseif ($REX['PAGEPATH'] == '' && $page == "content")
    {
      $page_name = $I18N->msg("content");
    }elseif($REX['PAGEPATH'] == '')
    {
      $page = "structure";
      $page_name = $I18N->msg("structure");
    }
  
  }
}

// ----- kein pagepath -> kein addon -> path setzen
if ($REX['PAGEPATH'] == '') $REX['PAGEPATH'] = $REX['INCLUDE_PATH']."/pages/$page.inc.php";

// ----- ausgabe des includes
if ($withheader) include $REX['INCLUDE_PATH']."/layout/top.php";
include $REX['PAGEPATH'];
if ($withheader) include $REX['INCLUDE_PATH']."/layout/bottom.php";

// ----- caching end für output filter
$CONTENT = ob_get_contents();
ob_end_clean();

// ----- EXTENSION POINT
$CONTENT = rex_register_extension_point( 'OUTPUT_FILTER', $CONTENT);

// ----- EXTENSION POINT - keine Manipulation der Ausgaben ab hier (read only)
rex_register_extension_point( 'OUTPUT_FILTER_CACHE', $CONTENT, '', true);

// ----- inhalt endgueltig ausgeben
echo $CONTENT;

?>