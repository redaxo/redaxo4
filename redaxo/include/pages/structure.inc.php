<?

/*
 * Todos: alle >> order << geschichten von vscope neu einbauen da nun mehrere
 * artikel mit gleichen id existieren und unterschiedliche clang haben
 * 
 * Alle Rechte sind jetzt gelöscht und müsste neu überlegt werden.....
 * 
 * verzeiht ;)
 * 
 * jan
 * 
 */


// --------------------------------------------- EXISTIERT DIESER ARTIKEL ?
if ($edit_id != "")
{
	$thisCat = new sql;
	$thisCat->setQuery("select * from rex_article where id='".$edit_id."' and clang=$clang");
	if ($thisCat->getRows()!=1) unset($edit_id);
}else
{
	unset($edit_id);
}

// --------------------------------------------- KATEGORIE PFAD UND RECHTE WERDEN ÜBERPRÜFT

include $REX[INCLUDE_PATH]."/functions/function_rex_category.inc.php";


// --------------------------------------------- TITLE

title($I18N->msg("title_structure"),"$KATout");

$add = "";
reset($REX[CLANG]);
if (count($REX[CLANG])>1)
{
	$add = "<table width=770 cellpadding=0 cellspacing=1 border=0><tr><td width=30 class=dgrey><img src=pics/leer.gif width=16 height=16 vspace=5 hspace=12></td><td class=dgrey>&nbsp;<b>Sprachen:</b> | ";
	while( list($key,$val) = each($REX[CLANG]) )
	{
		if ($key==$clang) $add .= "$val | ";
		else $add .= "<a href=index.php?page=structure&clang=$key&category_id=$category_id>$val</a> | "; 
	}
	$add .= "</b></td></tr></table><br>";
	echo $add;
}


// --------------------------------------------- KATEGORIE FUNKTIONEN

if ($function == "edit_category" && $edit_id != "")
{
	// --------------------- KATEGORIE EDIT
	$message = $I18N->msg("category_updated");
	$KAT->query("update rex_article set catname='$kat_name',updatedate='".time()."',updateuser='".$REX_USER->getValue("login")."' where id='$edit_id' and startpage=1 and clang=$clang");
	rex_generateArticle($edit_id);

}elseif ($function == "delete_category" && $edit_id != "")
{
	// --------------------- KATEGORIE DELETE
	$KAT = new sql;
	$KAT->setQuery("select * from rex_article where re_id='$edit_id' and clang='$clang' and startpage=1");
	if($KAT->getRows()==0)
	{
		$KAT->setQuery("select * from rex_article where re_id='$edit_id' and clang='$clang' and startpage=0");
		if($KAT->getRows()==0)
		{
			$message = rex_deleteArticle($edit_id);
		}else
		{
			$message = $I18N->msg("category_could_not_be_deleted")." ddd".$I18N->msg("category_still_contains_articles");
			$function = "edit";
		}
	}else
	{
		$message = $I18N->msg("category_could_not_be_deleted")." ".$I18N->msg("category_still_contains_subcategories");
		$function = "edit";

	}


}elseif ($function == "status" && $edit_id != "")
{
	// --------------------- KATEGORIE STATUS
	$KAT->setQuery("select * from rex_article where id='$edit_id' and clang=$clang and startpage=1");
	if ($KAT->getRows() == 1)
	{
		if ($KAT->getValue("status")==1) $newstatus = 0;
		else $newstatus = 1;
		$KAT->query("update rex_article set status='$newstatus',updatedate='".time()."',updateuser='".$REX_USER->getValue("login")."' where id='$edit_id' and clang=$clang and startpage=1");
		$message = $I18N->msg("category_status_updated");
		rex_generateArticle($edit_id);
	}else
	{
		$message = $I18N->msg("no_such_category");
	}
	

}elseif ($function == "add_category")
{
	// --------------------- KATEGORIE ADD
	$message = $I18N->msg("category_added_and_startarticle_created");
	$template_id = 0;
	if ($category_id!="")
	{
		$sql = new sql;
		$sql->setQuery("select template_id from rex_article where id=$category_id and startpage=1 and clang=$clang");
		if ($sql->getRows()==1) $template_id = $sql->getValue("template_id");
	}

	unset($id);
	reset($REX[CLANG]);
	while(list($key,$val)=each($REX[CLANG]))
	{
		$AART = new sql;
		$AART->setTable("rex_article");
		if (!$id) $id = $AART->setNewId("id");
		else $AART->setValue("id",$id);
		$AART->setValue("clang",$key);
		$AART->setValue("template_id",$template_id);
		$AART->setValue("name","$category_name");
		$AART->setValue("catname","$category_name");
		$AART->setValue("re_id",$category_id);
		$AART->setValue("prior",1);
		$AART->setValue("path",$KATPATH);
		$AART->setValue("startpage",1);
		$AART->setValue("status",1);
		$AART->setValue("online_from",time());
		$AART->setValue("online_to",mktime(0, 0, 0, 1, 1, 2010));
		$AART->setValue("createdate",time());
		$AART->setValue("createuser",$REX_USER->getValue("login"));
		$AART->setValue("updatedate",time());
		$AART->setValue("updateuser",$REX_USER->getValue("login"));
		$AART->insert();
	}
	rex_generateArticle($id);
}

// --------------------------------------------- ARTIKEL FUNKTIONEN

if ($function == "offline_article")
{
	// --------------------- ARTIKEL OFFLINE
	$EA = new sql;
	$EA->setTable("rex_article");
	$EA->where("id='$article_id' and clang=$clang");
	$EA->setValue("status",0);
	$EA->setValue("updatedate",time());
	$EA->setValue("updateuser",$REX_USER->getValue("login"));
	$EA->update();
	rex_generateArticle($article_id);
	$amessage = $I18N->msg("article_status_updated");

}else if ($function == "online_article")
{
	// --------------------- ARTIKEL ONLINE
	$EA = new sql;
	$EA->setTable("rex_article");
	$EA->where("id='$article_id' and clang=$clang");
	$EA->setValue("status",1);
	$EA->setValue("updatedate",time());
	$EA->setValue("updateuser",$REX_USER->getValue("login"));
	$EA->update();
	rex_generateArticle($article_id);
	$amessage = $I18N->msg("article_status_updated");

}else if ($function == "edit_article")
{
	// --------------------- ARTIKEL EDIT
	$amessage = $I18N->msg("article_updated");
	$EA = new sql;
	$EA->setTable("rex_article");
	$EA->where("id='$article_id' and clang=$clang");
	$EA->setValue("name",$article_name);
	$EA->setValue("template_id",$template_id);
	$EA->setValue("updatedate",time());
	$EA->setValue("updateuser",$REX_USER->getValue("login"));
	$EA->update();
	rex_generateArticle($article_id);

}elseif ($function == "delete_article")
{
	// --------------------- ARTIKEL DELETE
	$message = rex_deleteArticle($article_id);

}elseif ($function == "add_article")
{
	// --------------------- ARTIKEL ADD
	$amessage = $I18N->msg("article_added");
	unset($id);
	reset($REX[CLANG]);
	while(list($key,$val)=each($REX[CLANG]))
	{
		$AART = new sql;
		// $AART->debugsql = 1;
		$AART->setTable("rex_article");
		if (!$id) $id = $AART->setNewId("id");
		else $AART->setValue("id",$id);
		$AART->setValue("name",$article_name);
		$AART->setValue("catname",$article_name);
		$AART->setValue("clang",$key);
		$AART->setValue("re_id",$category_id);
		$AART->setValue("prior",$article_prior);
		$AART->setValue("path",$KATPATH);
		$AART->setValue("startpage",0);
		$AART->setValue("status",0);
		$AART->setValue("online_from",time());
		$AART->setValue("online_to",mktime(0, 0, 0, 1, 1, 2010));
		$AART->setValue("createdate",time());
		$AART->setValue("createuser",$REX_USER->getValue("login"));
		$AART->setValue("updatedate",time());
		$AART->setValue("updateuser",$REX_USER->getValue("login"));
		$AART->setValue("template_id",$template_id);
		$AART->insert();
	}
	rex_generateArticle($id);
}

// --------------------------------------------- KATEGORIE LISTE

if ($KATebene < $KatMaxEbenen) $addc = "<a href=index.php?page=structure&category_id=$category_id&function=add_cat&clang=$clang><img src=pics/folder_plus.gif width=16 height=16 border=0 alt=\"".$I18N->msg("add_category")."\"></a>";
else $addc = "&nbsp;";

echo	"<table border=0 cellpadding=5 cellspacing=1 width=770>
		<tr>
			<th width=30>$addc</th>
			<th align=left>".$I18N->msg("header_category")."</th>
			<th width=50 align=left>".$I18N->msg("header_priority")."</th>
			<th width=300 align=left>".$I18N->msg("header_edit_category")."</th>
			<th align=left width=153>".$I18N->msg("header_status")."</th>
		</tr>";

if ($message != "") echo "<tr><td align=center class=warning><img src=pics/warning.gif width=16 height=16></td><td colspan=4 class=warning><b>$message</b></td></tr>";
if ($category_id != 0) echo "<tr><td class=grey>&nbsp;</td><td class=grey colspan=4>..</td></tr>";

if ($function == "add_cat")
{
	// --------------------- KATEGORIE ADD FORM
	$echo .= "
		<tr>
			<form action=index.php><input type=hidden name=page value=structure>
			<input type=hidden name=category_id value=$category_id>
			<input type=hidden name=function value='add_category'>
			<input type=hidden name=clang value='$clang'>
			<td class=dgrey align=center><img src=pics/folder.gif width=16 height=16></td>
			<td class=dgrey><input type=text size=30 name=category_name></td>
			<td class=dgrey>&nbsp;<input type=text name=Position_New_Category value=\"1\" style='width:30px'></td>
			<td class=dgrey><input type=submit value='".$I18N->msg("add_category")."'></td>
			<td class=dgrey>&nbsp;</td>
			</form>
		</tr>";
}

// --------------------- KATEGORIE LIST

$KAT = new sql;
$KAT->setQuery("select * from rex_article where re_id='$category_id' and startpage=1 and clang=$clang order by prior");
for($i=0;$i<$KAT->getRows();$i++)
{
	$i_category_id = $KAT->getValue("id");

	if ($REX_USER->isValueOf("rights","catstructure[$i_category_id]") || $REX_USER->isValueOf("rights","catstructure[all]") || $REX_USER->isValueOf("rights","admin[]") || $REX_USER->isValueOf("rights","dev[]"))
	{
	
		if ($KAT->getValue("status") == 0)
		{
			$status_color="#aa0000";
			$kat_status = $I18N->msg("status_offline");
		}else
		{
			$status_color="#00aa00";
			$kat_status = $I18N->msg("status_online");
		}
	
		$kat_status = "<a href=index.php?page=structure&category_id=$category_id&edit_id=$i_category_id&function=status&clang=$clang><u><font color=$status_color>$kat_status</font></u></a>";
		$cat_pos++;
	
		if ($edit_id==$i_category_id and $function == "edit")
		{
	
			// --------------------- KATEGORIE EDIT FORM
	
			$echo .= "
				<tr>
					<td class=dgrey align=center><img src=pics/folder.gif width=16 height=16></td>
					<form action=index.php><input type=hidden name=page value=structure>
					<input type=hidden name=edit_id value=$edit_id>
					<input type=hidden name=category_id value=$category_id>
					<input type=hidden name=cid value=".$KAT->getValue("id").">
					<input type=hidden name=clang value=$clang>
					<td class=dgrey><input type=text size=30 name=kat_name value=\"".htmlentities($KAT->getValue("catname"))."\"></td>
					<td class=dgrey><input type=text name=Position_Category value=\"$cat_pos\" style='width:30px'></td>
					<td class=dgrey><input type=submit name=function value='edit_category'><input type=submit name=function value=delete_category></td>
					<td class=dgrey>$kat_status</td></form>
				</tr>";
		}else
		{
	
			$edit_txt = "<a href=index.php?page=structure&category_id=$category_id&edit_id=$i_category_id&function=edit&clang=$clang>".$I18N->msg("category_edit_delete")."&nbsp;</a>";
			// $edit_txt = $I18N->msg("no_permission_to_edit");
	
			$echo .= "
				<tr>
					<td class=grey align=center><img src=pics/folder.gif border=0 width=16 height=16 align=middle></td>
					<td class=grey><a href=index.php?page=structure&category_id=$i_category_id&clang=$clang>".$KAT->getValue("catname")."&nbsp;</a>";
			// $echo .= "[$i_category_id]";
			$echo .= "</td>";
	
			// $echo .= "<td class=grey valign=middle width=75><form method=post action=index.php?page=structure&category_id=".$category_id."&cid=".$KAT->getValue("id")."&clang=$clang style=display:inline><input type=field name=Position_Category style=width:30px;height:16px value=$cat_pos></form> <a href=index.php?page=structure&category_id=$category_id&order_id=".$KAT->getValue("prior")."&re_category=".$KAT->getValue("re_category_id")."&order=up&clang=$clang><img src=pics/pfeil_up.gif width=16 height=16 border=0 alt=up align=absmiddle></a><a href=index.php?page=structure&category_id=$category_id&order_id=".$KAT->getValue("prior")."&re_category=".$KAT->getValue("re_category_id")."&order=down><img src=pics/pfeil_down.gif width=16 height=16 border=0 alt=down align=absmiddle></a></td>";
			$echo .= "<td class=grey valign=middle width=20>$cat_pos</td>";
				
			$echo .= "
					<td class=grey>$edit_txt</td>
					<td class=grey>$kat_status</td>
				</tr>";
		}
	}
	$KAT->next();
}
echo $echo;
echo "</table>";




// --------------------------------------------- ARTIKEL LISTE





// --------------------- READ TEMPLATES

if($category_id > -1)
{
	$TEMPLATES = new sql;
	$TEMPLATES->setQuery("select * from rex_template");
	$TMPL_SEL = new select;
	$TMPL_SEL->set_name("template_id");
	$TMPL_SEL->set_size(1);
	$TMPL_SEL->set_style("width:150");
	$TMPL_SEL->add_option($I18N->msg("option_no_template"),"0");

	for ($i=0;$i<$TEMPLATES->getRows();$i++)
	{
		if ($TEMPLATES->getValue("active")==1)
		{
			$TMPL_SEL->add_option($TEMPLATES->getValue("name"),$TEMPLATES->getValue("id"));
		}
		$TEMPLATE_NAME[$TEMPLATES->getValue("id")] = $TEMPLATES->getValue("name");
		$TEMPLATES->nextValue();
	}
	$TEMPLATE_NAME[0] = $I18N->msg("template_default_name");



	// --------------------- ARTIKEL LIST

	echo "	<br><table border=0 cellpadding=5 cellspacing=1 width=770>
		<tr>
			<th width=30>";

	echo "<a href=index.php?page=structure&category_id=$category_id&function=add_art&clang=$clang><img src=pics/document_plus.gif width=16 height=16 border=0 alt=\"".$I18N->msg("article_add")."\"></a>";
	// echo "&nbsp;";

	echo "</td>
			<th align=left>".$I18N->msg("header_article_name")."</th>
			<th align=left width=50>".$I18N->msg("header_priority")."</th>
			<th align=left width=150>".$I18N->msg("header_template")."</th>
			<th align=left width=100>".$I18N->msg("header_date")."</th>
			<th align=left>&nbsp;</th>
			<th align=left colspan=3>".$I18N->msg("header_status")."</th>
		</tr>";

	if ($amessage != ""){ echo "<tr><td align=center class=warning><img src=pics/warning.gif width=16 height=16></td><td colspan=8 class=warning><b>$amessage</b></td></tr>"; }

	// --------------------- ARTIKEL ADD FORM

	if ($function=="add_art")
	{
		if ($template_id=="")
		{
			$sql = new sql;
			$sql->setQuery("select template_id from rex_article where re_id=$re_id and clang=$clang and startpage=1");
			if ($sql->getRows()==1) $TMPL_SEL->set_selected($sql->getValue("template_id"));
		}
		echo "<tr>
				<form action=index.php method=post>
				<input type=hidden name=page value=structure>
				<input type=hidden name=category_id value=$category_id>
				<input type=hidden name=clang value=$clang>
				<input type=hidden name=function value='add_article'>
				<td class=grey align=center><img src=pics/document.gif width=16 height=16 border=0></td>
				<td class=grey><input type=text name=article_name size=20></td>
				<td class=grey>&nbsp;<input type=text name=Position_New_Article value=\"1\" style='width:30px'></td>
				<td class=grey>".$TMPL_SEL->out()."</td>
				<td class=grey>".strftime($I18N->msg("adateformat"))."&nbsp;</td>
				<td class=grey><b>".$I18N->msg("article")."</b></td>
				<td class=grey colspan=3><input type=submit value='add_article'></td>
				</form>
				</tr>";
	}

	// --------------------- ARTIKEL LIST
	
	$sql = new sql;
	$sql->setQuery("select * 
			from 
				rex_article 
			where 
				((re_id='$category_id' and startpage=0) or (id='$category_id' and startpage=1)) 
				and clang=$clang  
			order by 
				prior,name");

	for($i=0;$i<$sql->getRows();$i++){

		if ($sql->getValue("startpage") == 1)
		{
			$startpage = $I18N->msg("start_article");
			$icon = "liste.gif";
		}else
		{
			$startpage = $I18N->msg("article");
			$icon = "document.gif";
		}

		$pos++;

		// --------------------- ARTIKEL EDIT FORM

		if ($function == "edit" and $sql->getValue("id") == $article_id){

			$TMPL_SEL->set_selected($sql->getValue("template_id"));

			echo "	<tr>
				<form action=index.php method=post>
				<input type=hidden name=page value=structure>
				<input type=hidden name=category_id value=$category_id>
				<input type=hidden name=article_id value=".$sql->getValue("id").">
				<input type=hidden name=function value='edit_article'>
				<input type=hidden name=aid value=".$sql->getValue("id").">
				<input type=hidden name=clang value=$clang>
				<td class=grey align=center><a href=index.php?page=content&article_id=".$sql->getValue("id")."&category_id=$category_id&clang=$clang><img src=pics/$icon width=16 height=16 border=0></a></td>
				<td class=grey><input type=text name=article_name value=\"".htmlentities($sql->getValue("name"))."\" size=20 style='width:100%'></td>
				<td class=grey>&nbsp;<input type=text name=Position_Article value=\"$pos\" style='width:30px'></td>
				<td class=grey>".$TMPL_SEL->out()."</td>
				<td class=grey>".strftime($I18N->msg("adateformat"),$sql->getValue("createdate"))."&nbsp;</td>
				<td class=grey><b>$startpage</b></td>
				<td class=grey colspan=3><input type=submit value='".$I18N->msg("edit")."'></td>
				</form>
				</tr>";

		// --------------------- ARTIKEL PERMISSION TO ENTER

		}elseif(1==1)
		{

			// --------------------- ARTIKEL NORMAL VIEW | EDIT AND ENTER

			echo "	<tr>
				<td class=grey align=center><a href=index.php?page=content&article_id=".$sql->getValue("id")."&category_id=$category_id&mode=edit&clang=$clang><img src=pics/$icon width=16 height=16 border=0></a></td>
				<td class=grey><a href=index.php?page=content&article_id=".$sql->getValue("id")."&category_id=$category_id&mode=edit&clang=$clang>".$sql->getValue("name")."&nbsp;</a>";

			// echo "[".$sql->getValue("id")."]";

			echo "</td>";

			// echo "<td class=grey align=center width=75 valign=middle><form method=post action=index.php?page=structure&category_id=".$category_id."&aid=".$sql->getValue("id")." style=display:inline><input type=field name=Position_Article style=width:30px;height:16px value=$pos></form> <a href=index.php?page=structure&category_id=$category_id&order_id=".$sql->getValue("prior")."&order=up><img src=pics/pfeil_up.gif border=0 alt=up align=absmiddle></a><a href=index.php?page=structure&category_id=$category_id&order_id=".$sql->getValue("prior")."&order=down><img src=pics/pfeil_down.gif border=0 alt=down align=absmiddle></a></td>";
			echo "<td class=grey align=center width=10 valign=middle>$pos</td>\n";

			echo "
				<td class=grey>".$TEMPLATE_NAME[$sql->getValue("template_id")]."</td>
				<td class=grey>".strftime($I18N->msg("adateformat"),$sql->getValue("createdate"))."&nbsp;</td>
				<td class=grey><b>$startpage</b></td>
				<td class=grey><a href=index.php?page=structure&article_id=".$sql->getValue("id")."&function=edit&category_id=$category_id&clang=$clang>".$I18N->msg("change")."</a></td>";

			if ($sql->getValue("startpage") == 1){
				echo "	<td class=grey><strike>".$I18N->msg("delete")."</strike></td>
						<td class=grey><strike>online</strike></td>";
			}else{
				if ($sql->getValue("status") == 0){ $article_status = "<a href=index.php?page=structure&article_id=".$sql->getValue("id")."&function=online_article&category_id=$category_id&clang=$clang><font color=#dd0000>".$I18N->msg("status_offline")."</font></a>"; }elseif( $sql->getValue("status") == 1){ $article_status = "<a href=index.php?page=structure&article_id=".$sql->getValue("id")."&function=offline_article&category_id=$category_id&clang=$clang><font color=#00dd00>".$I18N->msg("status_online")."</font></a>"; }
				echo "	<td class=grey><a href=index.php?page=structure&article_id=".$sql->getValue("id")."&function=delete_article&category_id=$category_id&clang=$clang>".$I18N->msg("delete")."</a></td>
						<td class=grey>$article_status</td>";
			}
			echo "</tr>";
		

		}elseif(1==0)
		{
			
			// --------------------- ARTIKEL NORMAL VIEW | NO EDIT ONLY ENTER
			
			echo "	<tr>
				<td class=grey align=center><a href=index.php?page=content&article_id=".$sql->getValue("id")."&category_id=$category_id&mode=edit&clang=$clang><img src=pics/$icon width=16 height=16 border=0></a></td>
				<td class=grey><a href=index.php?page=content&article_id=".$sql->getValue("id")."&category_id=$category_id&mode=edit&clang=$clang>".$sql->getValue("name")."&nbsp;</a></td>
				<td class=grey>$pos</td>
				<td class=grey>".$TEMPLATE_NAME[$sql->getValue("template_id")]."</td>
				<td class=grey>".strftime($I18N->msg("adateformat"),$sql->getValue("createdate"))."&nbsp;</td>
				<td class=grey><b>$startpage</b></td>
				<td class=grey><strike>".$I18N->msg("edit")."</strike></td>";

			if ($sql->getValue("startpage") == 1){
				echo "	<td class=grey><strike>delete</strike></td>
					<td class=grey><strike>".$I18N->msg("status_online")."</strike></td>";
			}else{
				echo "	<td class=grey><strike>".$I18N->msg("delete")."</strike></td>
					<td class=grey><strike>";
				if ($sql->getValue("status") == 0) echo "<font color=#dd0000>offline</font>";
				else echo "<font color=#00dd00>".$I18N->msg("status_online")."</font>";
				echo "	</strike></td>";
			}
			echo "</tr>";
			
		}else
		{
			
			// --------------------- ARTIKEL NORMAL VIEW | NO EDIT NO ENTER
			
			echo "	<tr>
				<td class=grey align=center><img src=pics/$icon width=16 height=16 border=0 align=middle></td>
				<td class=grey>".$sql->getValue("name")."</td>
				<td class=grey>$pos</td>
				<td class=grey>".$TEMPLATE_NAME[$sql->getValue("template_id")]."</td>
				<td class=grey>".strftime($I18N->msg("adateformat"),$sql->getValue("createdate"))."&nbsp;</td>
				<td class=grey><b>$startpage</b></td>
				<td class=grey><strike>".$I18N->msg("change")."</strike></td>
				<td class=grey><strike>".$I18N->msg("delete")."</strike></td>
				<td class=grey><strike>";
			if ($sql->getValue("status") == 0) echo "<font color=#dd0000>".$I18N->msg("status_offline")."</font>";
			else echo "<font color=#00dd00>".$I18N->msg("status_online")."</font>";
			echo "	</strike></td>";
			echo "</tr>";
		}
		$sql->counter++;
	}
}
echo "</table>";

?>