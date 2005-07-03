<?

$OUT = TRUE;
$type[add] = 1;
$type[edit] = 2;
$type[del] = 4;

if ($function == "delete")
{
	$del = new sql;
	$del->setQuery("select * from rex_module_action where action_id='$action_id'");	// module mit dieser aktion vorhanden ?
	
	if ($del->getRows()>0)
	{
		$module = "<font class=black>|</font> ";
		$modulname = htmlentities($del->getValue("rex_module_action.module_id"));
		for ($i=0;$i<$del->getRows();$i++)
		{
		 $module .= "<a href=index.php?page=module&function=edit&modul_id=".$del->getValue("rex_module_action.module_id").">".$del->getValue("rex_module_action.module_id")."</a> <font class=black>|</font> ";
		 $del->next();
		}
		
		$message = "<b>".$I18N->msg("action_cannot_be_deleted",$action_id)."</b><br> $module";
	}else
	{
		$del->query("delete from rex_action where id='$action_id'");
		$message = $I18N->msg("action_deleted");
	}
}

if ($function == "add" or $function == "edit")
{

	if ($save == "ja")
	{
		$faction = new sql;

		$sadd = 0;
		if (@in_array("1",$status)) $sadd = 1;
		$sedit = 0;
		if (@in_array("2",$status)) $sedit = 1;
		$sdelete = 0;
		if (@in_array("4",$status)) $sdelete = 1;

		if ($function == "add")
		{
			$faction->query("insert into rex_action (name,action,prepost,sadd,sedit,sdelete) VALUES ('$mname','$actioninput','$prepost','$sadd','$sedit','$sdelete')");
			$message = "<p class=warning>".$I18N->msg("action_added")."</p>";
		}else{
			$faction->query("update rex_action set name='$mname',action='$actioninput',prepost='$prepost',sadd='$sadd',sedit='$sedit',sdelete='$sdelete' where id='$action_id'");
			$message = "<p class=warning>".$I18N->msg("action_updated")."</p>";
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
			echo "	<tr><th colspan=3 align=left>".$I18N->msg("action_edit")." [ID=$action_id]</th></tr>";

			$hole = new sql;
			$hole->setQuery("select * from rex_action where id='$action_id'");
			$mname = $hole->getValue("name");
			$actioninput = $hole->getValue("action");
			$prepost = $hole->getValue("prepost");
			$sadd = $hole->getValue("sadd");
			$sedit = $hole->getValue("sedit");
			$sdelete = $hole->getValue("sdelete");
						
		}else{
			echo "	<tr><th colspan=3 align=left>".$I18N->msg("action_create")."</th></tr>";
			$prepost	= 0; // 0=pre / 1=post
			$sadd = 0;
			$sedit = 0;
			$sdelete = 0;
		}

		if ($message != "")
		{
			echo "<tr><td colspan=3 class=warning>$message</td></tr>";
		}

		$sel_prepost = new select();
		$sel_prepost->set_name("prepost");
		$sel_prepost->add_option($PREPOST[0],"0");
		$sel_prepost->add_option($PREPOST[1],"1");
		$sel_prepost->set_size(1);
		$sel_prepost->set_style("width:100px;");
		$sel_prepost->set_selected($prepost);

		$sel_status = new select();
		$sel_status->set_name("status[]");
		$sel_status->multiple(1);
		$sel_status->add_option($ASTATUS[0],"1");
		$sel_status->add_option($ASTATUS[1],"2");
		$sel_status->add_option($ASTATUS[2],"4");
		$sel_status->set_size(3);
		$sel_status->set_style("width:100px;");
		
		if ($sadd == 1) $sel_status->set_selected(1);
		if ($sedit == 1) $sel_status->set_selected(2);
		if ($sdelete == 1) $sel_status->set_selected(4);
		
		echo "	
			<form action=index.php method=post>
			<input type=hidden name=page value=module>
			<input type=hidden name=subpage value=actions>
			<input type=hidden name=function value=$function>
			<input type=hidden name=save value=ja>
			<input type=hidden name=action_id value=$action_id>
			<tr>
				<td width=100 class=grey>".$I18N->msg("action_name")."</td>
				<td class=grey colspan=2><input type=text size=10 name=mname value=\"".htmlentities($mname)."\" style='width:100%;'></td>
			</tr>
			<tr>
				<td valign=top class=grey>".$I18N->msg("input")."</td>
				<td class=grey colspan=2>
                  <textarea cols=20 rows=70 name=actioninput id=actioninput style='width:100%; height: 150;'>".htmlentities($actioninput)."</textarea>
                </td>
			</tr>";
			
		echo "
			<tr>
				<td align=right valign=middle class=grey>$PREPOST[0]/$PREPOST[1]</td>
				<td valign=middle class=grey colspan=2>".$sel_prepost->out()."</td>
			</tr>
			<tr>
				<td align=right valign=middle class=grey>STATUS</td>
				<td valign=middle class=grey colspan=2>".$sel_status->out()."</td>
			</tr>			
			<tr>
				<td class=grey>&nbsp;</td>
				<td class=grey width=200><input type=submit value='".$I18N->msg("save_action_and_quit")."'></td>
				<td class=grey>";
		
		if ($function != "add") echo "<input type=submit name=goon value='".$I18N->msg("save_action_and_continue")."'>";
		
		echo "</td>
			</tr>
			</form>
			</table>";

		$OUT = false;

	}
}

if ($OUT)
{
	// ausgabe actionsliste !
	echo "<table border=0 cellpadding=5 cellspacing=1 width=770>
		<tr>
			<th width=30><a href=index.php?page=module&subpage=actions&function=add><img src=pics/modul_plus.gif width=16 height=16 border=0 alt=\"".$I18N->msg("action_create")."\" title=\"".$I18N->msg("action_create")."\"></a></th>
			<th width=30>ID</th>
			<th align=left width=200>".$I18N->msg("action_name")."</th>
			<th align=left>".$I18N->msg("action_add")."</th>
			<th align=left>".$I18N->msg("action_edit")."</th>
			<th align=left>".$I18N->msg("action_delete")."</th>
			<th align=left>".$I18N->msg("action_functions")."</th>
		</tr>
		";
	
	if ($message != "")
	{
		echo "<tr>
			<td align=center class=warning><img src=pics/warning.gif width=16 height=16></td><td colspan=6 class=warning>$message</td></tr>";
	}
	
	$sql = new sql;
	$sql->setQuery("select * from rex_action order by name");
	
	for($i=0;$i<$sql->getRows();$i++){
	
		echo "	<tr bgcolor=#eeeeee>
				<td class=grey align=center><a href=index.php?page=module&subpage=actions&action_id=".$sql->getValue("id")."&function=edit><img src=pics/modul.gif width=16 height=16 border=0></a></td>
				<td class=grey align=center>".$sql->getValue("id")."</td>
				<td class=grey><a href=index.php?page=module&subpage=actions&action_id=".$sql->getValue("id")."&function=edit>".htmlentities($sql->getValue("name"))."</a> "." [".$PREPOST[$sql->getValue("prepost")]."]</td>
				<td class=grey>";
		if ($sql->getValue("sadd")==1) echo "X";
		echo "</td><td class=grey>";
		if ($sql->getValue("sedit")==1) echo "X";
		echo "</td><td class=grey>";
		if ($sql->getValue("sdelete")==1) echo "X";
		echo "</td>
				<td class=grey><a href=index.php?page=module&subpage=actions&action_id=".$sql->getValue("id")."&function=delete onclick='return confirm(\"".$I18N->msg('delete')." ?\")'>".$I18N->msg("action_delete")."</a></td>
			</tr>";
		$sql->counter++;
	}
	
	echo "</table>";
}

?>