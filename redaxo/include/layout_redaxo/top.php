<?
// changed 02.04.04 Carsten Eckelman <careck@circle42.com>
//   * i18n

echo "<html>
<head>
<title>".$REX[SERVERNAME]." - $page_name</title>
<link rel=stylesheet type=text/css href=css/style.css>
<script language=Javascript>
<!--
var redaxo = true;
//-->
</script>
</head>
<body bgcolor=#ffffff>
<table border=0 cellpadding=5 cellspacing=0 width=770>
<tr>
	<td colspan=3 class=grey align=right>".$REX[SERVERNAME]."</td>
</tr>
<tr>
	<td class=greenwhite><b>";

if ($LOGIN)
{
	echo "<a href=index.php?page=structure class=white>".$I18N->msg("structure")."</a> ";
	if ($REX_USER->isValueOf("rights","template[]")) echo " | <a href=index.php?page=template class=white>".$I18N->msg("template")."</a>";
	if ($REX_USER->isValueOf("rights","module[]")) echo " | <a href=index.php?page=module class=white>".$I18N->msg("module")."</a>"; 
	if ($REX_USER->isValueOf("rights","user[]")) echo " | <a href=index.php?page=user class=white>".$I18N->msg("user")."</a>"; 
	if ($REX_USER->isValueOf("rights","specials[]")) echo " | <a href=index.php?page=specials class=white>".$I18N->msg("specials")."</a>"; 
	if ($REX_USER->isValueOf("rights","import[]")) echo " | <a href=index.php?page=import class=white>".$I18N->msg("import")."</a>"; 
	if ($REX_USER->isValueOf("rights","community[]")) echo " | <a href=index.php?page=community class=white>".$I18N->msg("community")."</a>"; 
	if ($REX_USER->isValueOf("rights","stats[]")) echo " | <a href=index.php?page=stats class=white>".$I18N->msg("statistics")."</a>"; 
	echo " | <a href=index.php?FORM[logout]=1 class=white>".$I18N->msg("logout")."</a>";
}

echo "</b></td>";

if ($LOGIN) echo "<td align=right class=greenblack>".$I18N->msg("name").": <b>".$REX_USER->getValue("name")."</b></td>";
else echo "<td align=right class=greenblack>".$I18N->msg("logged_out")."</td>";

echo "</tr>
</table>";

?>