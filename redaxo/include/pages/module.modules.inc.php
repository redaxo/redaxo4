<?
	
$OUT = TRUE;

// ---------------------------- ACTIONSFUNKTIONEN FÜR MODULE

if ($function_action == "add")
{
	$aa = new sql;
	$aa->query("insert into rex_module_action set module_id='$modul_id',action_id='$action_id'");
	$message = $I18N->msg("action_taken");
	
}elseif($function_action == "delete")
{
	$aa = new sql;
	$aa->query("delete from rex_module_action where module_id='$modul_id' and id='$iaction_id'");
	$message = $I18N->msg("action_deleted_from_modul");
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
			$modultyp->query("insert into rex_modultyp (category_id,name,eingabe,ausgabe) VALUES ('$category_id','$mname','$eingabe','$ausgabe')");
			$message = "<p class=warning>".$I18N->msg("module_added")."</p>";
		}else{
			$modultyp->query("update rex_modultyp set name='$mname',eingabe='$eingabe',ausgabe='$ausgabe' where id='$modul_id'");
			$message = "<p class=warning>".$I18N->msg("module_updated")." | ".$I18N->msg("articel_updated")."</font></p>";
			
			// article updaten
			$gc = new sql;
			$gc->setQuery("select distinct(rex_article.id) from rex_article 
					left join rex_article_slice on rex_article.id=rex_article_slice.article_id 
					where rex_article_slice.modultyp_id='$modul_id'");
			for ($i=0;$i<$gc->getRows();$i++)
			{
				rex_generateArticle($gc->getValue("rex_article.id"));
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
		echo "<a name=edit><table class=rex style=table-layout:auto; cellpadding=5 cellspacing=1>";
	
		if ($function == "edit"){

			$hole = new sql;
			$hole->setQuery("select * from rex_modultyp where id='$modul_id'");
			$category_id	= $hole->getValue("category_id");
			$mname		= $hole->getValue("name");
			$include	= $hole->getValue("include");
			$ausgabe	= $hole->getValue("ausgabe");
			$eingabe	= $hole->getValue("eingabe");
						
			echo "	<tr><th colspan=3>".$I18N->msg("module_edit")." [ID=$modul_id]</th></tr>";

		}else{
			echo "	<tr><th colspan=3>".$I18N->msg("create_module")."</th></tr>";
		}

		if ($message != "")
		{
			echo "<tr class=warning><td colspan=3>$message</td></tr>";
		}

		echo "	
			<form action=index.php method=post>
			<input type=hidden name=page value=module>
			<input type=hidden name=function value=$function>
			<input type=hidden name=save value=ja>
			<input type=hidden name=category_id value=0>
			<input type=hidden name=modul_id value=$modul_id>
			<tr>
				<td width=100>".$I18N->msg("module_name")."</td>
				<td colspan=2><input type=text size=10 name=mname value=\"".htmlentities($mname)."\" style='width:100%;'></td>
			</tr>
			<tr>
				<td valign=top>".$I18N->msg("input")."</td>
				<td colspan=2>
                  <textarea cols=20 rows=70 name=eingabe id=eingabe style='width:100%; height: 150;'>".htmlentities($eingabe)."</textarea>
                </td>
			</tr>
			<tr>
				<td valign=top>".$I18N->msg("output")."</td>
				<td colspan=2>
                  <textarea cols=20 rows=70 name=ausgabe id=ausgabe style='width:100%; height: 150;'>".htmlentities($ausgabe)."</textarea>
                </td>
			</tr>";
			
		echo "
			<tr>
				<td>&nbsp;</td>
				<tdwidth=200><input type=submit value='".$I18N->msg("save_module_and_quit")."'></td>
				<td>";
		
		if ($function != "add") echo "<input type=submit name=goon value='".$I18N->msg("save_module_and_continue")."'>";
		
		echo "</td>
			</tr>
			</form>";

		if ($function == "edit")
		{
			
			$gaa = new sql;
			$gaa->setQuery("select * from rex_action order by name");

			if ($gaa->getRows()>0)
			{			
			
				echo "<tr><td colspan=3></td></tr><tr><td colspan=3 align=left><a name=action></a><b>".$I18N->msg("actions")."</b></td></tr>";
	
				$gma = new sql;
				$gma->setQuery("select * from rex_module_action,rex_action where rex_module_action.action_id=rex_action.id and rex_module_action.module_id='$modul_id'");
				for ($i=0;$i<$gma->getRows();$i++)
				{
					$iaction_id = $gma->getValue("rex_module_action.id");
					$action_id = $gma->getValue("rex_module_action.action_id");

					echo "<tr>
						<td>&nbsp;</td>
						<td>";
					
					echo "<a href=index.php?page=module&subpage=actions&action_id=$action_id&function=edit>".$gma->getValue("name")."</a>";
					echo " [";
					echo $PREPOST[$gma->getValue("prepost")];
					
					if ($gma->getValue("sadd")==1) echo "|".$ASTATUS[0];
					if ($gma->getValue("sedit")==1) echo "|".$ASTATUS[1];
					if ($gma->getValue("sdelete")==1) echo "|".$ASTATUS[2];
					
					echo "] </td>";
					echo "<td><a href=index.php?page=module&modul_id=$modul_id&function_action=delete&function=edit&iaction_id=$iaction_id onclick='return confirm(\"".$I18N->msg('delete')." ?\")'>".$I18N->msg("action_delete")."</a></td>";
					echo "</tr>";
					$gma->next();
				}
				
				$gaa_sel = new select();
				$gaa_sel->set_name("action_id");
				$gaa_sel->set_size(1);
				$gaa_sel->set_style("' class='inp100");
				
				for ($i=0;$i<$gaa->getRows();$i++)
				{
					$status = "";
					if ($gaa->getValue("sadd")==1) $status .= "|".$ASTATUS[0];
					if ($gaa->getValue("sedit")==1) $status .= "|".$ASTATUS[1];
					if ($gaa->getValue("sdelete")==1) $status .= "|".$ASTATUS[2];
					
					$gaa_sel->add_option($gaa->getValue("name")." [".$PREPOST[$gaa->getValue("prepost")]."$status]",$gaa->getValue("id"));
					$gaa->next();
				}

				echo "<form action=index.php#action method=post>";
				echo "<input type=hidden name=page value=module>";
				echo "<input type=hidden name=modul_id value=$modul_id>";
				echo "<input type=hidden name=function value=edit>";
				echo "<input type=hidden name=function_action value=add>";
				
				echo "<tr><td colspan=3></td></tr><tr>
					<td>&nbsp;</td>
					<td>".$gaa_sel->out()."</td>
					<td><input type=submit value='".$I18N->msg("action_add")."'></td>
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
	echo "<table class=rex style=table-layout:auto; cellpadding=5 cellspacing=1>
		<tr>
			<th width=30><a href=index.php?page=module&function=add><img src=pics/modul_plus.gif width=16 height=16 border=0 alt=\"".$I18N->msg("create_module")."\" title=\"".$I18N->msg("create_module")."\"></a></th>
			<th style=text-align:center; width=30>ID</th>
			<th width=300>".$I18N->msg("module_description")."</th>
			<th>".$I18N->msg("module_functions")."</th>
		</tr>
		";
	
	if ($message != "")
	{
		echo "<tr class=warning><td align=center><img src=pics/warning.gif width=16 height=16></td><td colspan=3>$message</td></tr>";
	}
	
	$sql = new sql;
	$sql->setQuery("select * from rex_modultyp order by name");
	
	for($i=0;$i<$sql->getRows();$i++){
	
		echo "	<tr>
				<td align=center><a href=index.php?page=module&modul_id=".$sql->getValue("id")."&function=edit><img src=pics/modul.gif width=16 height=16 border=0></a></td>
				<td align=center>".$sql->getValue("id")."</td>
				<td><a href=index.php?page=module&modul_id=".$sql->getValue("id")."&function=edit>".htmlentities($sql->getValue("name"))."</a>";
		
		if ($REX_USER->isValueOf("rights","expertMode[]")) echo " [".$sql->getValue("id")."]";
		
		echo "</td>
				<td><a href=index.php?page=module&modul_id=".$sql->getValue("id")."&function=delete onclick='return confirm(\"".$I18N->msg('delete')." ?\")'>".$I18N->msg("delete_module")."</a></td>
			</tr>";
		$sql->counter++;
	}
	
	echo "</table>";
}

?>