<?

// ---------------------------------------------------------- func

// ------------- generiere statische inhalte
$ERRMSG = "";

if ($func == "copyCategory")
{

	// noch nicht fertig


	// $which,$to_cat
	// copyCategory(10,0);



}elseif ($func == "generate")
{

	// ----------------------------------------------------------- generiere templates
	deleteDir($REX[INCLUDE_PATH]."/generated/templates",0);
	// mkdir($REX[INCLUDE_PATH]."/generated/templates",0664);
	$gt = new sql;
	$gt->setQuery("select * from rex_template");
	for ($i=0;$i<$gt->getRows();$i++)
	{
		$fp = fopen ($REX[INCLUDE_PATH]."/generated/templates/".$gt->getValue("rex_template.id").".template", "w");
		fputs($fp,$gt->getValue("rex_template.content"));
		fclose($fp);
		$gt->next();
	}

	// ----------------------------------------------------------- generiere artikel
	deleteDir($REX[INCLUDE_PATH]."/generated/articles",0);
	// mkdir($REX[INCLUDE_PATH]."/generated/articles",0664);
	$gc = new sql;
	$gc->setQuery("select * from rex_article");
	for ($i=0;$i<$gc->getRows();$i++)
	{
		generateArticle($gc->getValue("id"));
		$gc->next();
	}

	// ----------------------------------------------------------- generiere categorien
	deleteDir($REX[INCLUDE_PATH]."/generated/categories",0);
	// mkdir($REX[INCLUDE_PATH]."/generated/categories",0664);
	$gcc = new sql;
	$gcc->setQuery("select * from rex_category");
	for ($i=0;$i<$gcc->getRows();$i++)
	{
		generateCategory($gcc->getValue("id"));
		$gcc->next();
	}
	// generateCategories();

	$MSG = $I18N->msg('articles_generated')." ".$I18N->msg('old_articles_deleted');

}elseif($func == "linkchecker")
{
	unset($LART);

	for ($j=1;$j<11;$j++)
	{
		$LC = new sql;
		// $LC->debugsql = 1;
		$LC->setQuery("select rex_article_slice.article_id,rex_article_slice.id from rex_article_slice
				left join rex_article on rex_article_slice.link$j=rex_article.id
				where
				rex_article_slice.link$j>0 and rex_article.id IS NULL");
		for ($i=0;$i<$LC->getRows();$i++)
		{
			$LART[$LC->getValue("rex_article_slice.article_id")]=1;
			$LSLI[$LC->getValue("rex_article_slice.article_id")]=$LC->getValue("rex_article_slice.id");
			$LC->next();
		}
	}

	if (count($LART)>0) reset($LART);

	for ($i=0;$i<count($LART);$i++)
	{
		$MSG .= " | <a href=index.php?page=content&article_id=".key($LART)."&mode=edit&slice_id=".$LSLI[key($LART)]."&function=edit#editslice>".key($LART)."</a>";
		next($LART);
	}

	if (count($LART)==0) $MSG = $I18N->msg("links_ok");
	else $MSG = "<b>".$I18N->msg("links_not_ok")."</b> ". $MSG. " |";

}elseif($func == 'updateinfos')
{

	$h = fopen("include/master.inc.php","r");
	$cont = fread($h,filesize("include/master.inc.php"));

	$cont = ereg_replace("(REX\[BARRIEREFREI\].?\=.?)[^;]*","\\1".strtolower($neu_barriere),$cont);
	// $cont = ereg_replace("(REX\[COMMUNITY\].?\=.?)[^;]*","\\1".strtolower($neu_community),$cont);
	$cont = ereg_replace("(REX\[STARTARTIKEL_ID\].?\=.?)[^;]*","\\1".strtolower($neu_startartikel),$cont);
	$cont = ereg_replace("(REX\[error_emailaddress\].?\=.?)[^;]*","\\1\"".strtolower($neu_error_emailaddress)."\"",$cont);
	$cont = ereg_replace("(REX\[LANG\].?\=.?)[^;]*","\\1".$neu_lang,$cont);

	$cont = ereg_replace("(REX\[SERVER\].?\=.?)[^;]*","\\1\"".($neu_SERVER)."\"",$cont);
	$cont = ereg_replace("(REX\[SERVERNAME\].?\=.?)[^;]*","\\1\"".($neu_SERVERNAME)."\"",$cont);

	$cont = ereg_replace("(DB\[2\]\[HOST\].?\=.?)[^;]*","\\1\"".($neu_db2_host)."\"",$cont);
	$cont = ereg_replace("(DB\[2\]\[LOGIN\].?\=.?)[^;]*","\\1\"".($neu_db2_login)."\"",$cont);
	$cont = ereg_replace("(DB\[2\]\[PSW\].?\=.?)[^;]*","\\1\"".($neu_db2_psw)."\"",$cont);
	$cont = ereg_replace("(DB\[2\]\[NAME\].?\=.?)[^;]*","\\1\"".($neu_db2_name)."\"",$cont);
	// Caching
	$cont = ereg_replace("(REX\[CACHING\].?\=.?)[^;]*","\\1".strtolower($neu_caching),$cont);
	$cont = ereg_replace("(REX\[CACHING_DEBUG\].?\=.?)[^;]*","\\1".strtolower($neu_caching_debug),$cont);



	fclose($h);
	$h = fopen("include/master.inc.php","w+");
	fwrite($h,$cont,strlen($cont));
	fclose($h);

	if ($neu_barriere == "TRUE") $REX[BARRIEREFREI] = TRUE;
	else $REX[BARRIEREFREI] = FALSE;

	if ($neu_caching == "TRUE") $REX[CACHING] = TRUE;
	else $REX[CACHING] = FALSE;

	if ($neu_caching_debug == "TRUE") $REX[CACHING_DEBUG] = TRUE;
	else $REX[CACHING_DEBUG] = FALSE;

	/*
	if ($neu_community == "TRUE") $REX[COMMUNITY] = TRUE;
	else $REX[COMMUNITY] = FALSE;
	*/

	$REX[STARTARTIKEL_ID] = $neu_startartikel;
	$REX[error_emailaddress] = $neu_error_emailaddress;
	$REX[SERVER] = $neu_SERVER;
	$REX[SERVERNAME] = $neu_SERVERNAME;

	$DB[2][HOST] = $neu_db2_host;
	$DB[2][LOGIN] = $neu_db2_login;
	$DB[2][PSW] = $neu_db2_psw;
	$DB[2][NAME] = $neu_db2_name;

	$MSG = $I18N->msg("info_updated");

}

title($I18N->msg("specials_title"),"");

echo "<table border=0 cellpadding=5 cellspacing=1 width=770>
	<tr>
		<th align=left colspan=2>".$I18N->msg("special_features")."</th>
	</tr>";

if ($MSG != "") echo "<tr><td class=warning colspan=2><b>$MSG</b></td></tr>";

echo "<tr><td class=grey width=50% valign=top><br>";

echo "<b><a href=index.php?page=specials&func=generate>".$I18N->msg("regenerate_article")."</a></b><br>".$I18N->msg("regeneration_message")."<br><br>";
echo "<b><a href=index.php?page=specials&func=linkchecker>".$I18N->msg("link_checker")."</a></b><br>".$I18N->msg("check_links_text")."<br>";

echo "<br></td><td class=grey valign=top><br>";

echo "<table width=100% cellpadding=0 cellspacing=1 border=0>";
echo "<form action=index.php method=post>";
echo "<input type=hidden name=page value=specials>";
echo "<input type=hidden name=func value=updateinfos>";
echo "<tr><td colspan=3><b>".$I18N->msg("general_info_header")."</b></td></tr>";
echo "<tr><td width=170>\$REX[version]:</td><td width=10><img src=pics/leer.gif width=10 height=20></td><td>\"".$REX[version]."\"</td></tr>";
echo "<tr><td>\$REX[SERVER]:</td><td><img src=pics/leer.gif width=10 height=20></td><td><input type=text size=5 name=neu_SERVER value=\"".$REX[SERVER]."\" class=inp100></td></tr>";
echo "<tr><td>\$REX[SERVERNAME]:</td><td><img src=pics/leer.gif width=10 height=20></td><td><input type=text size=5 name=neu_SERVERNAME value=\"".$REX[SERVERNAME]."\" class=inp100></td></tr>";

echo "<tr><td colspan=3><br><b>".$I18N->msg("db1_can_only_be_changed_by_setup")."</b></td></tr>";

echo "<tr><td>\$DB[1][HOST]:</td><td><img src=pics/leer.gif width=10 height=20></td><td>\"".$DB[1][HOST]."\"</td></tr>";
echo "<tr><td>\$DB[1][LOGIN]:</td><td><img src=pics/leer.gif width=10 height=20></td><td>\"".$DB[1][LOGIN]."\"</td></tr>";
echo "<tr><td>\$DB[1][PSW]:</td><td><img src=pics/leer.gif width=10 height=20></td><td>-</td></tr>";
echo "<tr><td>\$DB[1][NAME]:</td><td><img src=pics/leer.gif width=10 height=20></td><td>\"".$DB[1][NAME]."\"</td></tr>";

echo "<tr><td colspan=3><br><b>".$I18N->msg("db2_text")."</b></td></tr>";

echo "<tr><td>\$DB[2][HOST]:</td><td><img src=pics/leer.gif width=10 height=20></td><td><input type=text size=5 name=neu_db2_host value=\"".$DB[2][HOST]."\" class=inp100></td></tr>";
echo "<tr><td>\$DB[2][LOGIN]:</td><td><img src=pics/leer.gif width=10 height=20></td><td><input type=text size=5 name=neu_db2_login value=\"".$DB[2][LOGIN]."\" class=inp100></td></tr>";
echo "<tr><td>\$DB[2][PSW]:</td><td><img src=pics/leer.gif width=10 height=20></td><td><input type=text size=5 name=neu_db2_psw value=\"".$DB[2][PSW]."\" class=inp100></td></tr>";
echo "<tr><td>\$DB[2][NAME]:</td><td><img src=pics/leer.gif width=10 height=20></td><td><input type=text size=5 name=neu_db2_name value=\"".$DB[2][NAME]."\" class=inp100></td></tr>";

echo "<tr><td colspan=3><br><b>".$I18N->msg("specials_others")."</b></td></tr>";

echo "<tr><td>\$REX[WWW_PATH]:</td><td><img src=pics/leer.gif width=10 height=20></td><td>\"".$REX[WWW_PATH]."\"</td></tr>";
echo "<tr><td>\$REX[INCLUDE_PATH]:</td><td><img src=pics/leer.gif width=10 height=20></td><td>\"".$REX[INCLUDE_PATH]."\"</td></tr>";

if($REX[BARRIEREFREI]) $barricheck = "selected"; else $barricheck_false = "selected";
// if($REX[COMMUNITY]) $communitycheck = "selected"; else $communitycheck_false = "selected";

echo "<tr><td>\$REX[error_emailaddress]:</td><td><img src=pics/leer.gif width=10 height=20></td><td><input type=text size=5 name=neu_error_emailaddress value=\"".$REX[error_emailaddress]."\" class=inp100></td></tr>";
echo "<tr><td>\$REX[BARRIEREFREI]:</td><td><img src=pics/leer.gif width=10 height=20></td><td><select name=neu_barriere size=1><option $barricheck>TRUE</option><option $barricheck_false>FALSE</option></select></td></tr>";
// echo "<tr><td>\$REX[COMMUNITY]:</td><td><img src=pics/leer.gif width=10 height=20></td><td><select name=neu_community size=1><option $communitycheck>TRUE</option><option $communitycheck_false>FALSE</option></select></td></tr>";
echo "<tr><td>\$REX[STARTARTIKEL_ID]:</td><td><img src=pics/leer.gif width=10 height=20></td><td><input type=text size=5 name=neu_startartikel value=\"".$REX[STARTARTIKEL_ID]."\"></td></tr>";
echo "<tr><td>\$REX[LANG]:</td><td><img src=pics/leer.gif width=10 height=20></td><td><select name=neu_lang size=1>";
foreach ($REX[LOCALES] as $l) {
	$selected = ($l == $REX[LANG] ? "selected" : "");
	echo "<option value='$l' $selected>$l</option>";
}
echo "</select></td></tr>";
if($REX_USER->isValueOf("rights","caching[]")){
	if($REX[CACHING]) $cachingcheck = "selected"; else $cachingcheck_false = "selected";
	echo "<tr><td>\$REX[CACHING]:</td><td><img src=pics/leer.gif width=10 height=20></td><td><select name=neu_caching size=1><option $cachingcheck>TRUE</option><option $cachingcheck_false>FALSE</option></select></td></tr>";
	if($REX[CACHING_DEBUG]) $cachingdebugcheck = "selected"; else $cachingdebugcheck_false = "selected";
	echo "<tr><td>\$REX[CACHING_DEBUG]:</td><td><img src=pics/leer.gif width=10 height=20></td><td><select name=neu_caching_debug size=1><option $cachingdebugcheck>TRUE</option><option $cachingdebugcheck_false>FALSE</option></select></td></tr>";
 }
echo "</select></td></tr>";
echo "<tr><td></td><td><img src=pics/leer.gif width=10 height=20></td><td><input type=submit name=sendit value=".$I18N->msg("specials_update")."></td></tr>";
echo "</form>";
echo "</table>";

echo "<br></td>
	</tr>
	</table>";

echo "<br>";

if ($function == "Update" or $function == "Ändern")
{
	$update = new sql;
	$update->setTable("rex_article_type");
	$update->where("type_id='$type_id'");
	$update->setValue("name",$name);
	$update->setValue("description",$description);
	$update->update();
	$type_id = 0;
	$function = "";
	$message = $I18N->msg("article_type_updated");
}elseif($function == "Delete" or $function == "Löschen")
{
	if ($type_id!=1)
	{
		$delete = new sql;
		$result = $delete->get_array("SELECT name,id FROM rex_article WHERE type_id = $type_id");
		if(is_array($result)){
			$message = $I18N->msg("article_type_still_used")."<br>";
			foreach($result as $var){
				$message.= "<br><a href=index.php?page=content&article_id=".$var[id]."&mode=meta target=_blank>".$var[name]."</a>";
			}
			$message.="<br><br>";
		} else {
	        $delete->query("delete from rex_article_type where type_id='$type_id'");
	        $delete->query("update rex_article set type_id='1' where type_id='$type_id'");
	        $message = $I18N->msg("article_type_deleted");
	    }
	}else
	{
		$message = $I18N->msg("article_type_could_not_be_deleted");
	}
}elseif($function == "add" && $save == 1)
{
	$add = new sql;
	$add->setTable("rex_article_type");
	$add->setValue("name",$name);
	$add->setValue("type_id",$type_id);
	$add->setValue("description",$description);
	$add->insert();
	$type_id = 0;
	$function = "";
	$message = $I18N->msg("article_type_added");
}



echo "	<table border=0 cellpadding=5 cellspacing=1 width=770>
	<tr>
		<th width=30><a href=index.php?page=specials&function=add>+</a></th>
		<th align=left width=30>".$I18N->msg("article_type_list_id")."</th>
		<th align=left width=250>".$I18N->msg("article_type_list_name")."</th>
		<th align=left colspan=2>".$I18N->msg("article_type_list_description")."</th>
	</tr>
	";

if ($message != "")
{
	echo "<tr><td align=center class=warning><img src=pics/warning.gif width=16 height=16></td><td colspan=5 class=warning>$message</td></tr>";
}

$sql = new sql;
$sql->setQuery("select * from rex_article_type order by type_id");

if ($function == "add")
{
	echo "	<tr>
		<form action=index.php method=post>
		<input type=hidden name=page value=specials>
		<input type=hidden name=save value=1>
		<td class=dgrey>&nbsp;</td>
		<td class=dgrey valign=top><input style='width:20' type=text size=20 maxlength=2 name=type_id value=\"".htmlentities($type_id)."\"></td>
		<td class=dgrey valign=top><input style='width:100%' type=text size=20 name=name value=\"".htmlentities($name)."\"></td>
		<td class=dgrey><input style='width:100%' type=text size=20 name=description value=\"".htmlentities($description)."\"></td>
		<td class=dgrey valign=top><input type=submit name=function value=add></td>
		</form>
		</tr>";
}



for($i=0;$i<$sql->getRows();$i++)
{
	if ($type_id == $sql->getValue("type_id"))
	{
		echo "	<tr>
			<form action=index.php method=post>
			<input type=hidden name=page value=specials>
			<input type=hidden name=type_id value=$type_id>
			<td class=dgrey>&nbsp;</td>
			<td class=dgrey valign=middle>".htmlentities($sql->getValue("type_id"))."</td>
			<td class=dgrey valign=top><input style='width:100%' type=text size=20 name=name value=\"".htmlentities($sql->getValue("name"))."\"></td>
			<td class=dgrey><input style='width:100%' type=text size=20 name=description value=\"".htmlentities($sql->getValue("description"))."\"></td>
			<td class=dgrey valign=top><input type=submit name=function value=".$I18N->msg("update_button")."><input type=submit name=function value=".$I18N->msg("delete_button")."></td>
			</form>
			</tr>";
	}else
	{
		echo "	<tr>
			<td class=grey>&nbsp;</td>
			<td class=grey>".htmlentities($sql->getValue("type_id"))."</td>
			<td class=grey><a href=index.php?page=specials&type_id=".$sql->getValue("type_id").">".htmlentities($sql->getValue("name"))."</a></td>
			<td class=grey colspan=2>".nl2br($sql->getValue("description"))."&nbsp;</td>
			</tr>";
	}
	$sql->counter++;
}

echo "</table>";


?>
