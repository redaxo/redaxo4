<?
// changed 02.04.04 Carsten Eckelman <careck@circle42.com>
//   * i18n

// --------------------------- globals

unset($REX);

$REX[HTDOCS_PATH] = "../";
$REX[GG] = false;
$REX[BF] = false;
$REX[REDAXO] = true;

include "include/master.inc.php";
// include $REX[INCLUDE_PATH]."/usercheck.inc.php";

session_start();

// benutzerauthentifizierung
$REX_LOGIN = new login();
$REX_LOGIN->setSqlDb(1);
$REX_LOGIN->setSysID("redaxo");	// fuer redaxo
$REX_LOGIN->setSessiontime(3000); // 3600 sekunden = 60 min
$REX_LOGIN->setLogin($REX_ULOGIN,$REX_UPSW);
if ($FORM[logout] == 1) $REX_LOGIN->setLogout(true);
$REX_LOGIN->setUserID("rex_user.user_id");
$REX_LOGIN->setUserquery("select * from rex_user where user_id='USR_UID'");
$REX_LOGIN->setLoginquery("select * from rex_user where login='USR_LOGIN' and psw='USR_PSW'");

if (!$REX_LOGIN->checkLogin())
{
	header("Location: login.php?"."&FORM[loginmessage]=".urlencode($REX_LOGIN->message));
	$LOGIN = FALSE;
	exit;
}else
{
	$LOGIN = TRUE;
	$REX_USER = $REX_LOGIN->USER;

	// echo "UID:".$REX_USER->getValue("rex_user.user_id");

}

$dl = false;
$page = strtolower($page);


if ($page=="specials" && $REX_USER->isValueOf("rights","specials[]"))
{
	$page_name = $I18N->msg("specials");
}elseif ($page=="module" && $REX_USER->isValueOf("rights","module[]"))
{
	$page_name = $I18N->msg("module");
}elseif ($page=="template" && $REX_USER->isValueOf("rights","template[]"))
{
	$page_name = $I18N->msg("template");
}elseif ($page=="user" && $REX_USER->isValueOf("rights","user[]"))
{
	$page_name = $I18N->msg("user");
}elseif ($page=="community" && $REX_USER->isValueOf("rights","community[]"))
{
	$page_name = $I18N->msg("community");
}elseif ($page=="import" && $REX_USER->isValueOf("rights","import[]"))
{
	$page_name = $I18N->msg("import");
}elseif ($page=="stats" && $REX_USER->isValueOf("rights","stats[]"))
{
	$page_name = "Statistiken";
}elseif ($page=="export" && $REX_USER->isValueOf("rights","export[]"))
{
	$dl = true;
}elseif ($page=="medienpool")
{
	$dl = true;
}elseif ($page=="linkmap")
{
	$dl = true;
}elseif ($page=="content")
{
	$page_name = $I18N->msg("content");
}else
{
	$page_name = $I18N->msg("structure");
	$page = "structure";
}

if (!$dl) include $REX[INCLUDE_PATH]."/layout_redaxo/top.php";
include $REX[INCLUDE_PATH]."/pages/$page.inc.php";
if (!$dl) include $REX[INCLUDE_PATH]."/layout_redaxo/bottom.php";

?>
