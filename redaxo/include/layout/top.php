<?

echo "<html>
<head>
	<title>".$REX[SERVERNAME]." - $page_name</title>
	<link rel=stylesheet type=text/css href=css/style.css>
	<script language=Javascript src=js/standard.js></script>
	<script language=Javascript>
	<!--
	var redaxo = true;
	//-->
	</script>
</head>
<body bgcolor=#ffffff onunload=closeAll();>
	<table border=0 cellpadding=5 cellspacing=0 width=770>
	<tr>
		<td colspan=2 class=grey align=right>".$REX[SERVERNAME]."</td>
	</tr>
	<tr>
		<td class=greenwhite width=550><b>";

if ($LOGIN)
{
	echo "<a href=index.php?page=structure class=white>".$I18N->msg("structure")."</a> ";
	if ($REX_USER->isValueOf("rights","mediapool[]")) echo " | <a href=# onclick=openREXMedialist(0); class=white>".$I18N->msg("pool_media")."</a>";
	if ($REX_USER->isValueOf("rights","template[]")) echo " | <a href=index.php?page=template class=white>".$I18N->msg("template")."</a>";
	if ($REX_USER->isValueOf("rights","module[]")) echo " | <a href=index.php?page=module class=white>".$I18N->msg("module")."</a>"; 
	if ($REX_USER->isValueOf("rights","user[]")) echo " | <a href=index.php?page=user class=white>".$I18N->msg("user")."</a>"; 
	if ($REX_USER->isValueOf("rights","addon[]")) echo " | <a href=index.php?page=addon class=white>".$I18N->msg("addon")."</a>"; 
	if ($REX_USER->isValueOf("rights","specials[]")) echo " | <a href=index.php?page=specials class=white>".$I18N->msg("specials")."</a>"; 
	if ($REX_USER->isValueOf("rights","stats[]")) echo " | <a href=index.php?page=stats class=white>".$I18N->msg("statistics")."</a>"; 

	reset($REX[ADDON][status]);
	for($i=0;$i<count($REX[ADDON][status]);$i++)
	{
		$apage = key($REX[ADDON][status]);
		$perm = $REX[ADDON][perm][$apage];
		$name = $REX[ADDON][name][$apage];
		if (current($REX[ADDON][status]) == 1 && ($REX_USER->isValueOf("rights",$perm) or $perm == "") )
		{
			echo " | <a href=index.php?page=$apage class=white>$name</a>";
		}
		next($REX[ADDON][status]);
	}

}

echo "</b></td>";

if ($LOGIN) echo "<td align=right class=greenblack valign=top>".$I18N->msg("name").": <b>".$REX_USER->getValue("name")."</b> [<a href=index.php?FORM[logout]=1 class=white><b>".$I18N->msg("logout")."</b></a>]</td>";
else echo "<td align=right class=greenblack valign=top><b>".$I18N->msg("logged_out")."</b></td>";

echo "</tr>
</table>";

?>