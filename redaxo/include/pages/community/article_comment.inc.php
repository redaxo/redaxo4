<?

title("<a href=index.php?page=community class=head>Community</a>: <a href=index.php?page=community&subpage=comment class=head>Artikelkommentarverwaltung </a>","","blue");

if ($func == "delete")
{
	$del = new sql;
	$del->query("delete from rex__article_comment where id='$com_id'");
	$FORM[err_msg] = "Artikelkommentar wurde gelöscht !";
}elseif($func == "online")
{
	$onl = new sql;
	$onl->query("update rex__article_comment set status=1 where id='$com_id'");
	$FORM[err_msg] = "Artikelkommentar wurde online gestellt !";
}elseif($func == "offline")
{
	$onl = new sql;
	$onl->query("update rex__article_comment set status=0 where id='$com_id'");
	$FORM[err_msg] = "Artikelkommentar wurde offline gestellt !";
}


echo "	<table border=0 cellpadding=5 cellspacing=1 width=770>
	<form action=index.php method=post>
	<input type=hidden name=page value=community>
	<input type=hidden name=subpage value=comment>
	<tr>
		<td width=150 class=blue>Kommentar</td>
		<td align=left class=dblue><input type=text size=10 style='width:100%' name=searchtxt value=\"$searchtxt\"></td>
		<td align=left width=200 class=dblue><select name=comstatus size=1 style='width:100%'>";

if ($comstatus == "") $comstatus = 0;

if ($comstatus == 1)
{
	echo "<option value=1 selected>Online</option>";
	echo "<option value=0>Offline</option>";
}else
{
	echo "<option value=1>Online</option>";
	echo "<option value=0 selected>Offline</option>";
}

echo "</select></td>
		<td align=left class=blue width=150><input type=submit value='Suche starten'></td>
	</tr>
	</form>
	</table><br>";

$COMMENTS = new sql;
// $COMMENTS->debugsql = 1;
$COMMENTS->setQuery("select * from rex__article_comment 
	left join rex__user on rex__article_comment.user_id=rex__user.id 
	left join rex_article on rex__article_comment.article_id=rex_article.id 
	where rex__article_comment.comment like '%$searchtxt%' and rex__article_comment.status='$comstatus' order by rex__article_comment.stamp LIMIT 20");

echo "	<table border=0 cellpadding=5 cellspacing=1 width=770>
	<form action=index.php method=post>
	<input type=hidden name=page value=community>
	<input type=hidden name=subpage value=comment>
	<input type=hidden name=save value=1>
	<tr>
		<td class=dblue width=15%>Username</td>
		<td class=dblue width=15%>Artikelname</td>
		<td class=dblue width=10%>Stamp</td>
		<td class=dblue width=60%>Kommentar</td>
	</tr>";

if ($FORM[err_msg] != "") echo "<tr><td colspan=4 class=warning>".$FORM[err_msg]."</td></tr>";


for($i=0;$i<$COMMENTS->getRows();$i++)
{
	
	echo "
		<tr>
			<td class=blue valign=top><a href=index.php?page=community&subpage=user&user_id=".$COMMENTS->getValue("rex__user.id").">".htmlentities($COMMENTS->getValue("rex__user.login"))."</a></td>
			<td class=blue valign=top><a href=/index.php?article_id=".$COMMENTS->getValue("rex_article.id")." target=_blank>".htmlentities($COMMENTS->getValue("rex_article.name"))."</a></td>
			<td class=blue valign=top>".date("d. M Y - H:i",$COMMENTS->getValue("rex__article_comment.stamp"))."</td>
			<td class=blue>".nl2br(htmlentities($COMMENTS->getValue("rex__article_comment.comment")))."<br>
			<br><a href=index.php?page=community&subpage=comment&com_id=".$COMMENTS->getValue("rex__article_comment.id")."&func=online&searchtxt=".urlencode($searchtxt)."&comstatus=$comstatus>Online stellen</a>
			 | <a href=index.php?page=community&subpage=comment&com_id=".$COMMENTS->getValue("rex__article_comment.id")."&func=offline&searchtxt=".urlencode($searchtxt)."&comstatus=$comstatus>Offline stellen</a>
			 | <a href=index.php?page=community&subpage=comment&com_id=".$COMMENTS->getValue("rex__article_comment.id")."&func=delete&searchtxt=".urlencode($searchtxt)."&comstatus=$comstatus>Löschen</a>
			
			</td>
		</tr>";

	$COMMENTS->next();
}

echo "</table>";



?>