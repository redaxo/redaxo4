<?

title($I18N->msg("title_templates"),"");

$OUT = TRUE;

if ($function == "delete")
{
	$del = new sql;
	$del->setQuery("select rex_article.id,rex_template.name from rex_article 
			left join rex_template on rex_article.template_id=rex_template.id 
			where rex_article.template_id='$template_id' LIMIT 0,10");	
	
	if ($template_id==1)
	{
		$message = $I18N->msg("cant_delete_default_template");
	}else if ($del->getRows() >0)
	{
		$message = $I18N->msg("cant_delete_template_because_its_in_use",htmlentities($del->getValue("rex_template.name")));
	}else
	{
		$del->query("delete from rex_template where id='$template_id'");
		$message = $I18N->msg("template_deleted");

		deleteDir($REX[INCLUDE_PATH]."/generated/templates/".$template_id.".template");
	}
}

if ($function == "add" or $function == "edit"){

	if ($save == "ja")
	{
		
		if ($function == "add")
		{
			$ITPL = new sql;
			$ITPL->setTable("rex_template");
			$ITPL->setValue("name",$templatename);
			$ITPL->setValue("active",$active);
			$ITPL->setValue("content",$content);
			if ($REX[BCONTENT]) $ITPL->setValue("bcontent",$bcontent);
			$ITPL->insert();
			$template_id = $ITPL->last_insert_id;
			$message = $I18N->msg("template_added");
		}else{
			$TMPL = new sql;
			$TMPL->setTable("rex_template");
			$TMPL->where("id='$template_id'");
			$TMPL->setValue("name",$templatename);
			$TMPL->setValue("content",$content);
			$TMPL->setValue("active",$active);
			if ($REX[BCONTENT]) $TMPL->setValue("bcontent",$bcontent);
			$TMPL->update();
			$message = $I18N->msg("template_added");
		}	

		$gt = new sql;
		$gt->setQuery("select * from rex_template where id='$template_id'");

		$fp = fopen ($REX[INCLUDE_PATH]."/generated/templates/".$template_id.".template", "w");
		fputs($fp,$gt->getValue("content"));
		fclose($fp);

		if ($REX[BCONTENT])
		{
			$fp = fopen ($REX[INCLUDE_PATH]."/generated/templates/".$template_id.".btemplate", "w");
			fputs($fp,$gt->getValue("bcontent"));
			fclose($fp);
		}

		if ($goon != "")
		{
			$function = "edit";
			$save = "nein";
		}else
		{
			$function = "";
		}
	}

	if ($save != "ja")
	{
		echo "<a name=edit><table border=0 cellpadding=5 cellspacing=1 width=770>";
	
		if ($function == "edit"){
			echo "	<tr><th align=left colspan=3><b>".$I18N->msg("edit_template")."</b></th></tr>";

			$hole = new sql;
			$hole->setQuery("select * from rex_template where id='$template_id'");
			$templatename	= $hole->getValue("name");
			$content	= $hole->getValue("content");
			$active	= $hole->getValue("active");
			
			if ($REX[BCONTENT]) $bcontent = $hole->getValue("bcontent");

		}else{
			echo "	<tr><th align=left colspan=3>".$I18N->msg("edit_template")."</th></tr>";
		}

		echo "	<form action=index.php method=post>
			<input type=hidden name=page value=template>
			<input type=hidden name=function value=$function>
			<input type=hidden name=save value=ja>
			<input type=hidden name=template_id value=$template_id>
			<tr>
				<td width=100 class=grey>".$I18N->msg("template_name")."</td>
				<td class=grey colspan=2><input type=text size=10 name=templatename value=\"".htmlentities($templatename)."\" style='width:100%;'></td>
			</tr>";
		
		echo "
			<tr>
				<td width=100 class=grey align=right><input type=checkbox name=active value=1";
		if ($active==1) echo " checked";
		echo "></td>
				<td class=grey colspan=2>".$I18N->msg("checkbox_template_active")."</td>
			</tr>";
		
		echo "
			<tr>
				<td class=grey>&nbsp;</td>
				<td class=grey width=200><input type=submit value='".$I18N->msg("save_template_and_quit")."'></td>
				<td class=grey><input type=submit name=goon value='".$I18N->msg("save_template_and_continue")."'></td>
			</tr>";
		
		echo "
			<tr>
				<td valign=top class=grey>".$I18N->msg("header_template")."</td>
				<td class=grey colspan=2><textarea name=content cols=40 rows=5 style='width: 100%;height: 400px;'>".htmlspecialchars($content)."</textarea></td>
			</tr>";
		
		if ($REX[BCONTENT])
		{
			echo "<tr>
				<td valign=top class=grey>".$I18N->msg("header_template")."<br>[".$I18N->msg("accessible")."]</td>
				<td class=grey colspan=2><textarea name=bcontent cols=40 rows=5 style='width: 100%;height: 400px;'>".htmlspecialchars($bcontent)."</textarea></td>
			</tr>";	
		}
		
		
		echo "	</form>";		
		echo "</table>";

		$OUT = false;

	}
}

if ($OUT)
{
	// ausgabe templateliste !
	echo "<table border=0 cellpadding=5 cellspacing=1 width=770>
		<tr>
			<th width=30><a href=index.php?page=template&function=add><img src=pics/template_plus.gif width=16 height=16 border=0></a></th>
			<th align=left width=300>".$I18N->msg("header_template_description")."</th>
			<th align=left width=50>".$I18N->msg("header_template_active")."</th>
			<th align=left>".$I18N->msg("header_template_functions")."</th>
		</tr>
		";
	
	if ($message != "")
	{
		echo "<tr><td align=center class=warning><img src=pics/warning.gif width=16 height=16></td><td colspan=3 class=warning>$message</td></tr>";
	}
	
	$sql = new sql;
	$sql->setQuery("select * from rex_template order by name");
	
	for($i=0;$i<$sql->getRows();$i++)
	{
		echo "	<tr>
				<td class=grey align=center><a href=index.php?page=template&template_id=".$sql->getValue("id")."&function=edit><img src=pics/template.gif width=16 height=16 border=0></a></td>
				<td class=grey><a href=index.php?page=template&template_id=".$sql->getValue("id")."&function=edit>".htmlentities($sql->getValue("name"))."</a>";
		
		if ($REX_USER->isValueOf("rights","expertMode[]")) echo " [".$sql->getValue("id")."]";
			
		echo "</td>
				<td class=grey>";
			
		if ($sql->getValue("active")==1) echo $I18N->msg("yes");
		else echo $I18N->msg("no");

		echo "</td>
				<td class=grey><a href=index.php?page=template&template_id=".$sql->getValue("id")."&function=delete>".$I18N->msg("delete_template")."</a></td>
			</tr>";
		$sql->counter++;
	}
	
	echo "</table>";
}

?>