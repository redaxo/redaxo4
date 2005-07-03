<?

unset($REX);

$REX[HTDOCS_PATH] = "../";
$REX[GG] = false;

include "./include/master.inc.php";

// ----------------- CREATE LANG OBJ
$I18N = new i18n($REX[LANG],$REX[INCLUDE_PATH]."/lang/");
$REX[LOCALES] = i18n::getLocales($REX[INCLUDE_PATH]."/lang/");
setlocale(LC_ALL,trim($I18N->msg("setlocale")));

$LOGIN = FALSE;

include $REX[INCLUDE_PATH]."/layout/top.php";

title("Login","");

if ($FORM[loginmessage] != "")
{
	echo "<table border=0 cellpadding=5 cellspacing=1 width=770>
	<tr><td align=center class=warning width=40><img src=pics/warning.gif width=16 height=16></td>
	<td class=warning>".$FORM[loginmessage]."</td></tr>
	</table>";
}

echo "<br><table border=1 cellpadding=5 cellspacing=0 width=770>
<tr>
	<td class=dgrey>
		<table width=200 cellpadding=3 cellspacing=0 border=0>
		<tr>
			<td valign=middle><form action=index.php method=post><input type=hidden name=page value=structure>".$I18N->msg('login_name').":</td>
			<td valign=middle><input type=text size=10 value='$REX_ULOGIN' name=REX_ULOGIN></td>
			<td valign=middle>&nbsp;</td>
		</tr>
		<tr>
			<td valign=middle>".$I18N->msg('password').":</td>
			<td valign=middle><input type=password size=10 name=REX_UPSW></td>
			<td valign=middle><input type=submit value=".$I18N->msg('login')."></td>
		</tr></form>
		</table>
	</td>
</tr>
</table>
";

include $REX[INCLUDE_PATH]."/layout/bottom.php";

?>