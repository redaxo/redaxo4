<?php

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

// ----------------- AUTH
if ($REX['SETUP'])
{
   // ----------------- SET SETUP LANG
	if ($lang != "en_gb" & $lang != "de_de" ) $lang = "de_de";
	$REX['LANG'] = $lang;
	
	// ----------------- CREATE LANG OBJ
    rex_create_lang( $REX['LANG']);
	setlocale(LC_ALL,trim($I18N->msg("setlocale")));
	header('Content-Type: text/html; charset='.$I18N->msg("htmlcharset"));
	
	$page_name = $I18N->msg("setup");
	$page = "setup";
	$dl = false;
	
}else
{
	$REX_LOGIN = new login();
	$REX_LOGIN->setSqlDb(1);
	$REX_LOGIN->setSysID($REX['INSTNAME']); // fuer redaxo
	$REX_LOGIN->setSessiontime(3000); // 3600 sekunden = 60 min
	$REX_LOGIN->setLogin($REX_ULOGIN,$REX_UPSW);
	if ($FORM['logout'] == 1) $REX_LOGIN->setLogout(true);
	$REX_LOGIN->setUserID("rex_user.user_id");
	$REX_LOGIN->setUserquery("select * from rex_user where user_id='USR_UID'");
	$REX_LOGIN->setLoginquery("select * from rex_user where login='USR_LOGIN' and psw='USR_PSW'");
	if (!$REX_LOGIN->checkLogin())
	{
		header("Location: login.php?"."&FORM[loginmessage]=".urlencode($REX_LOGIN->message));
		header('Content-Type: text/html; charset='.$I18N->msg("htmlcharset"));
		$LOGIN = FALSE;
		exit;
	}else
	{
		$LOGIN = TRUE;
		$REX_USER = $REX_LOGIN->USER;
	}
	
	// ----------------- CREATE LANG OBJ
	/*
	if ($REX_USER->isValueOf("rights","be_lang[de_de]")) $REX[LANG] = "de_de";
	else if ($REX_USER->isValueOf("rights","be_lang[en_gb]")) $REX[LANG] = "en_gb";
	*/
    rex_create_lang( $REX['LANG']);
	setlocale(LC_ALL,trim($I18N->msg("setlocale")));
	header('Content-Type: text/html; charset='.$I18N->msg("htmlcharset"));

	$dl = false;
	$page = strtolower($page);
	
	if ($page=="addon" && ($REX_USER->isValueOf("rights","addon[]") || $REX_USER->isValueOf("rights","dev[]")))
	{
		$page_name = $I18N->msg("addon");
	}elseif ($page=="specials" && ($REX_USER->isValueOf("rights","specials[]") || $REX_USER->isValueOf("rights","dev[]")))
	{
		$page_name = $I18N->msg("specials");
	}elseif ($page=="module" && ($REX_USER->isValueOf("rights","module[]") || $REX_USER->isValueOf("rights","dev[]")))
	{
		$page_name = $I18N->msg("module");
	}elseif ($page=="template" && ($REX_USER->isValueOf("rights","template[]") || $REX_USER->isValueOf("rights","dev[]")))
	{
		$page_name = $I18N->msg("template");
	}elseif ($page=="user" && ($REX_USER->isValueOf("rights","user[]") || $REX_USER->isValueOf("rights","admin[]")))
	{
		$page_name = $I18N->msg("user");
	}elseif ($page=="medienpool")
	{
		$dl = true;
	}elseif ($page=="linkmap")
	{
		$dl = true;
	}elseif ($page=="content")
	{
		$page_name = $I18N->msg("content");
	}elseif ($page=="structure")
	{
		$page_name = $I18N->msg("structure");
	}else
	{
		
		// --- keine page gefunden
		// --- addon check
		$as = array_search($page,$REX['ADDON']['page']);
		if ($as === false || $page == "")
		{
			// --- kein addon gefunden -> structure
			$page_name = $I18N->msg("structure");
			$page = "structure";
		}else
		{
			// --- addon gefunden	
			$perm = $REX['ADDON']['perm'][$as];
			// --- right checken
			if($REX_USER->isValueOf("rights",$perm) or $perm == "" or $REX_USER->isValueOf("rights","admin[]"))
			{
				$dl = true;
				$REX['PAGEPATH'] = $REX['INCLUDE_PATH']."/addons/$page/pages/index.inc.php";
			}else
			{
				// --- no perms to this addon
				$page_name = $I18N->msg("structure");
				$page = "structure";
			}
		}
	}
}


// ----- kein pagepath -> kein addon -> path setzen
if ($REX['PAGEPATH'] == "") $REX['PAGEPATH'] = $REX['INCLUDE_PATH']."/pages/$page.inc.php";


// ----- ausgabe des includes
if (!$dl) include $REX['INCLUDE_PATH']."/layout/top.php";
include $REX['PAGEPATH'];
if (!$dl) include $REX['INCLUDE_PATH']."/layout/bottom.php";


// ----- caching end für output filter
$CONTENT = ob_get_contents();
ob_end_clean();


// ---- user functions vorhanden ? wenn ja ausführen
if (is_array($REX['OUTPUT_FILTER']))
{
	reset ($REX['OUTPUT_FILTER']);
	for ($i=0;$i<count($REX['OUTPUT_FILTER']);$i++)
	{
		$CONTENT = call_user_func(current($REX['OUTPUT_FILTER']), $CONTENT);
	}
}


// ---- caching functions vorhanden ? wenn ja ausführen
if (is_array($REX['OUTPUT_FILTER_CACHE']))
{
	reset ($REX['OUTPUT_FILTER_CACHE']);
	for ($i=0;$i<count($REX['OUTPUT_FILTER_CACHE']);$i++)
	{
		call_user_func(current($REX['OUTPUT_FILTER_CACHE']), $CONTENT);
	}
}

// ----- inhalt endgueltig ausgeben
echo $CONTENT;

?>