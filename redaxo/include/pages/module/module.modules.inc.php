<?
	
$OUT = TRUE;

// ---------------------------- ACTIONSFUNKTIONEN FÜR MODULE

if ($REX_USER->isValueOf("rights","action[]"))
{
	$aa = new sql;
	if ($function_action == "add")
	{
		$aa->query("insert into rex_module_action set module_id='$modul_id',action_id='$action_id'");
		$message = $I18N->msg("action_taken");
		
	}elseif($function_action == "delete")
	{
		$aa->query("delete from rex_module_action where module_id='$modul_id' and id='$iaction_id'");
		$message = $I18N->msg("action_deleted_from_modul");
	}	
}



// ---------------------------- FUNKTIONEN FÜR MODULE

if ($function == "delete")
{
	$del = new sql;
	$del->setQuery("select distinct rex_article_slice.article_id,rex_modultyp.name from rex_article_slice 
			left join rex_modultyp on rex_article_slice.modultyp_id=rex_modultyp.id 
			where rex_article_slice.modultyp_id='$modul_id'");	
	
	if ($del->getRows() >0)
	{
		$module = "<font class=black>|</font> ";
		$modulname = htmlentities($del->getValue("rex_modultyp.name"));
		for ($i=0;$i<$del->getRows();$i++)
		{
		 $module .= "<a href=index.php?page=content&article_id=".$del->getValue("rex_article_slice.article_id").">".$del->getValue("rex_article_slice.article_id")."</a> <font class=black>|</font> ";
		 $del->next();
		}
		
		$message = "<b>".$I18N->msg("module_cannot_be_deleted",$modulname)."</b><br> $module";
	}else
	{
		$del->query("delete from rex_modultyp where id='$modul_id'");
		$message = $I18N->msg("module_deleted");
	}
}

if ($function == "add" or $function == "edit")
{

	if ($save == "ja")
	{
		$modultyp = new sql;

		if ($function == "add")
		{
			if ($REX[BARRIEREFREI]) $modultyp->query("insert into rex_modultyp (category_id,name,eingabe,ausgabe,bausgabe) VALUES ('$category_id','$name','$eingabe','$ausgabe','$bausgabe')");
			else $modultyp->query("insert into rex_modultyp (category_id,name,eingabe,ausgabe) VALUES ('$category_id','$name','$eingabe','$ausgabe')");
			$message = "<p class=warning>".$I18N->msg("module_added")."</p>";
		}else{
			if ($REX[BARRIEREFREI]) $modultyp->query("update rex_modultyp set name='$name',eingabe='$eingabe',ausgabe='$ausgabe',bausgabe='$bausgabe',php_enable='$php_enable',html_enable='$html_enable' where id='$modul_id'");
			else $modultyp->query("update rex_modultyp set name='$name',eingabe='$eingabe',ausgabe='$ausgabe',php_enable='$php_enable',html_enable='$html_enable' where id='$modul_id'");
			$message = "<p class=warning>".$I18N->msg("module_updated")." | ".$I18N->msg("articel_updated")."</font></p>";
			
			// article updaten
			$gc = new sql;
			$gc->setQuery("select distinct(rex_article.id) from rex_article 
					left join rex_article_slice on rex_article.id=rex_article_slice.article_id 
					where rex_article_slice.modultyp_id='$modul_id'");
			for ($i=0;$i<$gc->getRows();$i++)
			{
				generateArticle($gc->getValue("rex_article.id"));
				$gc->next();
			}
		}
		
		if ($goon != "")
		{
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
			echo "	<tr><th colspan=3 align=left>".$I18N->msg("module_edit")."</th></tr>";

			$hole = new sql;
			$hole->setQuery("select * from rex_modultyp where id='$modul_id'");
			$category_id	= $hole->getValue("category_id");
			$name		= $hole->getValue("name");
			$include	= $hole->getValue("include");
			$ausgabe	= $hole->getValue("ausgabe");
			if ($REX[BARRIEREFREI]) $bausgabe = $hole->getValue("bausgabe");
			$eingabe	= $hole->getValue("eingabe");
			$html_on	= $hole->getValue("html_enable");
			$php_on		= $hole->getValue("php_enable");
						
		}else{
			echo "	<tr><th colspan=3 align=left>".$I18N->msg("create_module")."</th></tr>";
			$html_on	= 0;
			$php_on		= 0;
		}

		if ($html_on == 1) $HTML_ON = " checked";
		if ($php_on == 1) $PHP_ON = " checked";

		if ($message != "")
		{
			echo "<tr><td colspan=3 class=warning>$message</td></tr>";
		}

		echo "	
			<form action=index.php method=post>
			<input type=hidden name=page value=module>
			<input type=hidden name=function value=$function>
			<input type=hidden name=save value=ja>
			<input type=hidden name=category_id value=0>
			<input type=hidden name=modul_id value=$modul_id>
			<tr>
				<td width=100 class=grey>".$I18N->msg("module_name")."</td>
				<td class=grey colspan=2><input type=text size=10 name=name value=\"".htmlentities($name)."\" style='width:100%;'></td>
			</tr>
			<tr>
				<td valign=top class=grey>".$I18N->msg("input")."</td>
				<td class=grey colspan=2><textarea cols=20 rows=70 name=eingabe style='width:100%; height: 150;'>".htmlentities($eingabe)."</textarea></td>
			</tr>
			<tr>
				<td valign=top class=grey>".$I18N->msg("output")."</td>
				<td class=grey colspan=2><textarea cols=20 rows=70 name=ausgabe style='width:100%; height: 150;'>".htmlentities($ausgabe)."</textarea></td>
			</tr>";
		
		if ($REX[BARRIEREFREI])
		{
			echo "	<tr>
				<td valign=top class=grey>".$I18N->msg("output")." <br>[".$I18N->msg("accessible")."]</td>
				<td class=grey colspan=2><textarea cols=20 rows=70 name=bausgabe style='width:100%; height: 150;'>".htmlentities($bausgabe)."</textarea></td>
				</tr>";
		}
			
		echo "
			<tr>
				<td align=right valign=middle class=grey><input type=checkbox name=php_enable value=1 $PHP_ON></td>
				<td valign=middle class=grey colspan=2>".$I18N->msg("allowed_for_php")."</td>
			</tr>
			<tr>
				<td align=right valign=middle class=grey><input type=checkbox name=html_enable value=1 $HTML_ON></td>
				<td valign=middle class=grey colspan=2>".$I18N->msg("allowed_for_html")."</td>
			</tr>			
			<tr>
				<td class=grey>&nbsp;</td>
				<td class=grey width=200><input type=submit value='".$I18N->msg("save_module_and_quit")."'></td>
				<td class=grey>";
		
		if ($function != "add") echo "<input type=submit name=goon value='".$I18N->msg("save_module_and_continue")."'>";
		
		echo "</td>
			</tr>
			</form>";

		if ($REX_USER->isValueOf("rights","action[]") && $function == "edit")
		{
			
			$gaa = new sql;
			$gaa->setQuery("select * from rex_action order by status");

			if ($gaa->getRows()>0)
			{			
			
				echo "<tr><td colspan=3></td></tr><tr><td colspan=3 align=left class=dgrey><a name=action></a><b>".$I18N->msg("actions")."</b></td></tr>";
	
				$gma = new sql;
				$gma->setQuery("select * from rex_module_action,rex_action where rex_module_action.action_id=rex_action.id and rex_module_action.module_id='$modul_id'");
				for ($i=0;$i<$gma->getRows();$i++)
				{
					$iaction_id = $gma->getValue("rex_module_action.id");
					$action_id = $gma->getValue("rex_module_action.action_id");

					echo "<tr>
						<td class=grey>&nbsp;</td>
						<td class=grey>";
					
					echo "<a href=index.php?page=module&subpage=actions&action_id=$action_id&function=edit>".$gma->getValue("name")."</a>";
					echo " [";
					echo $PREPOST[$gma->getValue("prepost")]."|";
					echo $ASTATUS[$gma->getValue("status")];
					echo "] </td>";
					echo "<td class=grey><a href=index.php?page=module&modul_id=$modul_id&function_action=delete&function=edit&iaction_id=$iaction_id>".$I18N->msg("action_delete")."</a></td>";
					echo "</tr>";
					$gma->next();
				}
				
				$gaa_sel = new select();
				$gaa_sel->set_name("action_id");
				$gaa_sel->set_size(1);
				$gaa_sel->set_style("' class='inp100");
				
				
				for ($i=0;$i<$gaa->getRows();$i++)
				{
					$gaa_sel->add_option($gaa->getValue("name")." [".$PREPOST[$gaa->getValue("prepost")]."|".$ASTATUS[$gaa->getValue("status")]."]",$gaa->getValue("id"));
					$gaa->next();
				}

				echo "<form action=index.php#action method=post>";
				echo "<input type=hidden name=page value=module>";
				echo "<input type=hidden name=modul_id value=$modul_id>";
				echo "<input type=hidden name=function value=edit>";
				echo "<input type=hidden name=function_action value=add>";
				
				echo "<tr><td colspan=3></td></tr><tr>
					<td class=grey>&nbsp;</td>
					<td class=grey>".$gaa_sel->out()."</td>
					<td class=grey><input type=submit value='".$I18N->msg("action_add")."'></td>
					</tr>";
				
				echo "</form>";

			
			}

		}
	
		echo "</table>";
	
		$OUT = false;

	}
}

if ($OUT)
{
	// ausgabe modulliste !
	echo "<table border=0 cellpadding=5 cellspacing=1 width=770>
		<tr>
			<th width=30><a href=index.php?page=module&function=add><img src=pics/modul_plus.gif width=16 height=16 border=0></a></th>
			<th align=left width=300>".$I18N->msg("module_description")."</th>
			<th align=left>".$I18N->msg("module_functions")."</th>
			<th align=left width=100>PHP</th>
			<th align=left width=100>HTML</th>
		</tr>
		";
	
	if ($message != "")
	{
		echo "<tr><td align=center class=warning><img src=pics/warning.gif width=16 height=16></td><td colspan=5 class=warning>$message</td></tr>";
	}
	
	
	$sql = new sql;
	$sql->setQuery("select * from rex_modultyp order by name");
	
	for($i=0;$i<$sql->getRows();$i++){
	
		echo "	<tr bgcolor=#eeeeee>
				<td class=grey align=center><img src=pics/modul.gif width=16 height=16></td>
				<td class=grey><a href=index.php?page=module&modul_id=".$sql->getValue("id")."&function=edit>".htmlentities($sql->getValue("name"))."</a>";
		
		if ($REX_USER->isValueOf("rights","expertMode[]")) echo " [".$sql->getValue("id")."]";
		
		echo "</td>
				<td class=grey><a href=index.php?page=module&modul_id=".$sql->getValue("id")."&function=delete>".$I18N->msg("delete_module")."</a></td>
				<td class=grey>";
		if ($sql->getValue("php_enable")==1) echo $I18N->msg("yes");
		else echo "&nbsp;";
		echo "</td>
				<td class=grey>";
		if ($sql->getValue("html_enable")==1) echo $I18N->msg("yes");
		echo "</td>
			</tr>";
		$sql->counter++;
	}
	
	echo "</table>";
}

?>