<?

// --------------------------------------------- permissions
if ($edit_id != "")
{
	$thisCat = new sql;
	$thisCat->setQuery("select * from rex_category where id='".$edit_id."'");
	if ($thisCat->getRows()!=1)
	{
		unset($edit_id);
	}
}else
{
	unset($edit_id);
}
// vscope change prior position
if($Position_Article){
	$sql = new sql;
	$sql->order_position($Position_Article,$aid,"id","rex_article","prior","category_id",$category_id);
	generateArticle($aid);
}
if($Position_Category){
	$sql = new sql;
	$sql->order_position($Position_Category,$cid,"id","rex_category","prior","re_category_id",$category_id);
	generateCategory($category_id);
	generateCategoryList($category_id);
}


// vscope order up/down script
if($order!=''){
	$sql = new sql;

	if($re_category!=''){
		$o_which = 're_category_id';
		$o_cat = $re_category;
		$o_table = 'rex_category';
	} else {
		$o_which = 'category_id';
		$o_cat = $category_id;
		$o_table = 'rex_article';
	}

	if($order=='up'){
		$sql->order_up($order_id,$o_table,'prior',$o_which,$o_cat);
	}
	if($order=='down'){
		$sql->order_down($order_id,$o_table,'prior',$o_which,$o_cat);
	}
	if($order=='top'){
		$sql->order_top($order_id,$o_table,'prior',$o_which,$o_cat);
	}
	if($order=='bottom'){
		$sql->order_bottom($order_id,$o_table,'prior',$o_which,$o_cat);
	}

	// generate articles an cats
	if($o_table == "rex_category"){
		generateCategory($o_cat);
		generateCategoryList($o_cat);
	}
	if($o_table == "rex_article"){
		$res = $sql->get_array("SELECT id FROM rex_article WHERE category_id = $o_cat AND prior = $order_id");
		generateArticle($res[0][id]);
	}

}

$STRUCTURE_PERM = FALSE;
if ($REX_USER->isValueOf("rights","structure[all]")) $STRUCTURE_PERM = TRUE;

// --------------------------------------------- category pfad

include $REX[INCLUDE_PATH]."/functions/function_rex_category.inc.php";
title($I18N->msg("title_structure"),$KATout);

// --------------------------------------------- name check $kat_name, $category_name


// --------------------------------------------- category functions

if ($function == "edit_category" && $STRUCTURE_PERM && $edit_id != "")
{
	$message = $I18N->msg("category_updated");
	$KAT->query("update rex_category set name='$kat_name' where id='$edit_id'");
	generateCategory($edit_id);
}

if ($function == "delete_category" && $STRUCTURE_PERM && $edit_id != "")
{
	$KAT->setQuery("select * from rex_category where re_category_id='$edit_id'");
	if($KAT->getRows()==0)
	{

		$KAT->setQuery("select * from rex_article where category_id='$edit_id' and startpage=0");
		if($KAT->getRows()==0)
		{
			$message = deleteCategory($edit_id);

		}else
		{
			$message = $I18N->msg("category_could_not_be_deleted")." ".$I18N->msg("category_still_contains_articles");
			$function = "edit";
		}
	}else
	{
		$message = $I18N->msg("category_could_not_be_deleted")." ".$I18N->msg("category_still_contains_subcategories");
		$function = "edit";

	}
}

if ($function == "status" && $STRUCTURE_PERM && $edit_id != "")
{
	$KAT->setQuery("select * from rex_category where id='$edit_id'");
	if ($KAT->getRows() == 1)
	{
		if ($KAT->getValue("status")==1) $KAT->query("update rex_category set status='0' where id='$edit_id'");
		if ($KAT->getValue("status")==0) $KAT->query("update rex_category set status='1' where id='$edit_id'");
		$message = $I18N->msg("category_status_updated");

		generateCategory($edit_id);

	}else
	{
		$message = $I18N->msg("no_such_category");
	}
}

if ($function == "add_category" && $STRUCTURE_PERM)
{
	$message = $I18N->msg("category_added_and_startarticle_created");

	$AKAT = new sql;
	// vscope prior script
	$category_prior = $AKAT->new_order('rex_category','prior','re_category_id',$category_id);
	$AKAT->setTable("rex_category");
	$AKAT->setValue("name",$category_name);
	$AKAT->setValue("re_category_id",$category_id);
	$AKAT->setValue("prior",$category_prior);
	$AKAT->setValue("path",$KATSQLpath);
	$AKAT->setValue("status",0);
	$AKAT->insert();

	$AART = new sql;
	$AART->setTable("rex_article");
	$AART->setValue("name","$category_name");
	$AART->setValue("category_id",$AKAT->last_insert_id);
	$AART->setValue("prior",1);
	$AART->setValue("path",$KATSQLpath."-".$AKAT->last_insert_id);
	$AART->setValue("startpage",1);
	$AART->setValue("status",1);
	$AART->setValue("online_von",date("YmdHis"));
	$AART->setValue("online_bis","20100101");
	$AART->setValue("erstelldatum",date("Ymd"));

	if ($category_id!="")
	{
		$sql = new sql;
		$sql->setQuery("select template_id from rex_article where category_id=$category_id and startpage=1");
		if ($sql->getRows()==1) $AART->setValue("template_id",$sql->getValue("template_id"));
		else $AART->setValue("template_id",0);
	}else
	{
		$AART->setValue("template_id",0);
	}

	$AART->insert();

	$sql = new sql;
	$sql->order_position($Position_New_Category,$AKAT->last_insert_id,"id","rex_category","prior","re_category_id",$category_id);

	generateCategory($AKAT->last_insert_id);
	generateArticle($AART->last_insert_id);

}


// --------------------------------------------- article functions

if ($function == "offline_article" && ($STRUCTURE_PERM))
{
	$amessage = $I18N->msg("article_status_updated");
	$KAT->query("update rex_article set status='0' where id='$article_id'");
	generateArticle($article_id);
}

if ($function == "online_article" && ($STRUCTURE_PERM))
{
	$amessage = $I18N->msg("article_status_updated");
	$KAT->query("update rex_article set status='1' where id='$article_id'");
	generateArticle($article_id);
}

if ($function == "edit_article" && ($STRUCTURE_PERM || $REX_USER->isValueOf("rights","article[$article_id]")))
{
	$amessage = $I18N->msg("article_updated");
	$KAT->query("update rex_article set name='$article_name',template_id='$template_id' where id='$article_id'");

	generateArticle($article_id);
}

if ($function == "delete_article" && ($STRUCTURE_PERM))
{
	$message = deleteArticle($article_id);
}

if ($function == "add_article" and $STRUCTURE_PERM)
{
	$amessage = $I18N->msg("article_added");
	$AART = new sql;

	// vscope prior script
	$article_prior = $AART->new_order('rex_article','prior','category_id',$category_id);
	$AART->setTable("rex_article");
	$AART->setValue("name",$article_name);
	$AART->setValue("category_id",$category_id);
	$AART->setValue("prior",$article_prior);
	$AART->setValue("path",$KATSQLpath);
	$AART->setValue("startpage",0);
	$AART->setValue("status",0);
	$AART->setValue("online_von",date("YmdHis"));
	$AART->setValue("online_bis","20100101");
	$AART->setValue("erstelldatum",date("Ymd"));
	$AART->setValue("template_id",$template_id);
	$AART->insert();

	// now set right position
	$sql = new sql;
	$sql->order_position($Position_New_Article,$AART->last_insert_id,"id","rex_article","prior","category_id",$category_id);
	generateArticle($AART->last_insert_id);
}

// --------------------------------------------- category pfad

// echo "-".$path."-";


// --------------------------------------------- category list

if ($KATebene < $KatMaxEbenen && ($REX_USER->isValueOf("rights","structure[all]") || $STRUCTURE_PERM)) $addc = "<a href=index.php?page=structure&category_id=$category_id&function=add_cat><img src=pics/folder_plus.gif width=16 height=16 border=0 alt=\"".$I18N->msg("add_category")."\"></a>";
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
if ($category_id != 0) echo "<tr><td class=grey>&nbsp;</td><td class=grey colspan=4><a href=index.php?page=structure&category_id=$re_category_id>..</a></td></tr>";

if ($function == "add_cat")
{
	$echo .= "
		<tr>
			<form action=index.php><input type=hidden name=page value=structure>
			<input type=hidden name=category_id value=$category_id>
			<input type=hidden name=function value='add_category'>
			<td class=dgrey align=center><img src=pics/folder.gif width=16 height=16></td>
			<td class=dgrey><input type=text size=30 name=category_name></td>
			<td class=dgrey>&nbsp;<input type=text name=Position_New_Category value=\"1\" style='width:30px'></td>
			<td class=dgrey><input type=submit value='".$I18N->msg("add_category")."'></td>
			<td class=dgrey>&nbsp;</td>
			</form>
		</tr>";
}

$KAT->setQuery("select * from rex_category where re_category_id='$category_id' order by prior");
for($i=0;$i<$KAT->getRows();$i++)
{

	$i_category_id = $KAT->getValue("id");

	if ($KAT->getValue("status") == 0)
	{
		$status_color="#aa0000";
		$kat_status = $I18N->msg("status_offline");
	}else
	{
		$status_color="#00aa00";
		$kat_status = $I18N->msg("status_online");
	}

	if ($STRUCTURE_PERM)
	{
		$kat_status = "<a href=index.php?page=structure&category_id=$category_id&edit_id=$i_category_id&function=status><u><font color=$status_color>$kat_status</font></u></a>";
	}else
	{
		$kat_status = "<font color=$status_color>$kat_status</font>";
	}

	$cat_pos++;

	if ($edit_id==$i_category_id and $function == "edit" && $STRUCTURE_PERM)
	{
		$echo .= "
			<tr>
				<td class=dgrey align=center><img src=pics/folder.gif width=16 height=16></td>
				<form action=index.php><input type=hidden name=page value=structure><input type=hidden name=edit_id value=$edit_id><input type=hidden name=category_id value=$category_id><input type=hidden name=cid value=".$KAT->getValue("id").">
				<td class=dgrey><input type=text size=30 name=kat_name value=\"".htmlentities($KAT->getValue("name"))."\"></td>
				<td class=dgrey><input type=text name=Position_Category value=\"$cat_pos\" style='width:30px'></td>
				<td class=dgrey><input type=submit name=function value='edit_category'><input type=submit name=function value=delete_category></td>
				<td class=dgrey>$kat_status</td></form>
			</tr>";
	}else
	{

		if ($STRUCTURE_PERM) $edit_txt = "<a href=index.php?page=structure&category_id=$category_id&edit_id=$i_category_id&function=edit>".$I18N->msg("category_edit_delete")."&nbsp;</a>";
		else $edit_txt = $I18N->msg("no_permission_to_edit");

		$echo .= "
			<tr>
				<td class=grey align=center><img src=pics/folder.gif border=0 width=16 height=16 align=middle></td>
				<td class=grey><a href=index.php?page=structure&category_id=$i_category_id>".$KAT->getValue("name")."&nbsp;</a>";
		if ($REX_USER->isValueOf("rights","expertMode[]")) $echo .= "[$i_category_id]";
		$echo .= "</td>";
		if ($STRUCTURE_PERM){
			if ($REX_USER->isValueOf("rights","editPrio[]")){
	            $echo.= "
	                <td class=grey valign=middle width=75><form method=post action=index.php?page=structure&category_id=".$category_id."&cid=".$KAT->getValue("id")." style=display:inline><input type=field name=Position_Category style=width:30px;height:16px value=$cat_pos></form> <a href=index.php?page=structure&category_id=$category_id&order_id=".$KAT->getValue("prior")."&re_category=".$KAT->getValue("re_category_id")."&order=up><img src=pics/pfeil_up.gif width=16 height=16 border=0 alt=up align=absmiddle></a><a href=index.php?page=structure&category_id=$category_id&order_id=".$KAT->getValue("prior")."&re_category=".$KAT->getValue("re_category_id")."&order=down><img src=pics/pfeil_down.gif width=16 height=16 border=0 alt=down align=absmiddle></a></td>
	            ";
	        } else {
	            $echo.= "
	                <td class=grey valign=middle width=20>$cat_pos</td>
	            ";
	        }
		} else {
	        $echo.= "
	        	<td class=grey valign=middle width=20>$cat_pos</td>
	        ";
	    }
		$echo.= "
				<td class=grey>$edit_txt</td>
				<td class=grey>$kat_status</td>
			</tr>";
	}
	$KAT->next();
}

echo $echo;

echo "</table>";



// --------------------------------------------- article list
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

	echo "	<br><table border=0 cellpadding=5 cellspacing=1 width=770>
		<tr>
			<th width=30>";

	if ($STRUCTURE_PERM) echo "<a href=index.php?page=structure&category_id=$category_id&function=add_art><img src=pics/document_plus.gif width=16 height=16 border=0 alt=\"".$I18N->msg("article_add")."\"></a>";
	else echo "&nbsp;";

	echo "</td>
			<th align=left>".$I18N->msg("header_article_name")."</th>
			<th align=left width=50>".$I18N->msg("header_priority")."</th>
			<th align=left width=150>".$I18N->msg("header_template")."</th>
			<th align=left width=100>".$I18N->msg("header_date")."</th>
			<th align=left>&nbsp;</th>
			<th align=left colspan=3>".$I18N->msg("header_status")."</th>
		</tr>";

	if ($amessage != ""){ echo "<tr><td align=center class=warning><img src=pics/warning.gif width=16 height=16></td><td colspan=8 class=warning><b>$amessage</b></td></tr>"; }

	if ($function=="add_art")
	{

		// ---------------- ARTIKEL ERSTELLEN FORM / DEFAULT TEMPLATE

		if ($template_id=="")
		{
			$sql = new sql;
			$sql->setQuery("select template_id from rex_article where category_id=$category_id and startpage=1");
			if ($sql->getRows()==1) $TMPL_SEL->set_selected($sql->getValue("template_id"));
		}

		echo "	<tr>
			<form action=index.php method=post>
			<input type=hidden name=page value=structure>
			<input type=hidden name=category_id value=$category_id>
			<td class=grey align=center><img src=pics/document.gif width=16 height=16 border=0></td>
			<td class=grey><input type=hidden name=function value='add_article'><input type=text name=article_name size=20></td>
			<td class=grey>&nbsp;<input type=text name=Position_New_Article value=\"1\" style='width:30px'></td>
			<td class=grey>".$TMPL_SEL->out()."</td>
			<td class=grey>".date_from_mydate(date("YmdHis"),"")."&nbsp;</td>
			<td class=grey><b>".$I18N->msg("article")."</b></td>
			<td class=grey colspan=3><input type=submit value='add_article'></td>
			</form>
			</tr>";
	}

	// Check startArticle[] Permissons
	$startSQL = "";
	if($REX_USER->isValueOf("rights","startArticle[none]") ){
		if(!$REX_USER->isValueOf("rights","startArticle[$category_id]")){
	      	$startSQL = "AND startpage = 0";
	    }
	}
	$sql = new sql;
	$sql->setQuery("select * from rex_article where category_id='$category_id' $startSQL order by prior,name");


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

		if ($function == "edit" and $sql->getValue("id") == $article_id and $STRUCTURE_PERM){

			$TMPL_SEL->set_selected($sql->getValue("template_id"));

			echo "	<tr>
				<form action=index.php method=post>
				<input type=hidden name=page value=structure>
				<input type=hidden name=category_id value=$category_id>
				<input type=hidden name=article_id value=".$sql->getValue("id").">
				<input type=hidden name=function value='edit_article'>
				<input type=hidden name=aid value=".$sql->getValue("id").">
				<td class=grey align=center><a href=index.php?page=content&article_id=".$sql->getValue("id")."&category_id=$category_id><img src=pics/$icon width=16 height=16 border=0></a></td>
				<td class=grey><input type=text name=article_name value=\"".htmlentities($sql->getValue("name"))."\" size=20 style='width:100%'></td>
				<td class=grey>&nbsp;<input type=text name=Position_Article value=\"$pos\" style='width:30px'></td>
				<td class=grey>".$TMPL_SEL->out()."</td>
				<td class=grey>".date_from_mydate($sql->getValue("erstelldatum"),"")."&nbsp;</td>
				<td class=grey><b>$startpage</b></td>
				<td class=grey colspan=3><input type=submit value='".$I18N->msg("edit")."'></td>
				</form>
				</tr>";

		}elseif($STRUCTURE_PERM)
		{
			echo "	<tr>
				<td class=grey align=center><a href=index.php?page=content&article_id=".$sql->getValue("id")."&category_id=$category_id&mode=edit><img src=pics/$icon width=16 height=16 border=0></a></td>
				<td class=grey><a href=index.php?page=content&article_id=".$sql->getValue("id")."&category_id=$category_id&mode=edit>".$sql->getValue("name")."&nbsp;</a>";

			if ($REX_USER->isValueOf("rights","expertMode[]")) echo "[".$sql->getValue("id")."]";

			echo "</td>";

			if ($REX_USER->isValueOf("rights","editPrio[]")){
				echo "<td class=grey align=center width=75 valign=middle><form method=post action=index.php?page=structure&category_id=".$category_id."&aid=".$sql->getValue("id")." style=display:inline><input type=field name=Position_Article style=width:30px;height:16px value=$pos></form> <a href=index.php?page=structure&category_id=$category_id&order_id=".$sql->getValue("prior")."&order=up><img src=pics/pfeil_up.gif border=0 alt=up align=absmiddle></a><a href=index.php?page=structure&category_id=$category_id&order_id=".$sql->getValue("prior")."&order=down><img src=pics/pfeil_down.gif border=0 alt=down align=absmiddle></a></td>";
			} else {
				echo "<td class=grey align=center width=10 valign=middle>$pos</td>\n";
			}

			echo "
				<td class=grey>".$TEMPLATE_NAME[$sql->getValue("template_id")]."</td>
				<td class=grey>".date_from_mydate($sql->getValue("erstelldatum"),"")."&nbsp;</td>
				<td class=grey><b>$startpage</b></td>
				<td class=grey><a href=index.php?page=structure&article_id=".$sql->getValue("id")."&function=edit&category_id=$category_id>".$I18N->msg("change")."</a></td>";

			if ($sql->getValue("startpage") == 1){
				echo "	<td class=grey><strike>löschen</strike></td>
					<td class=grey><strike>online</strike></td>";
			}else{
				if ($sql->getValue("status") == 0){ $article_status = "<a href=index.php?page=structure&article_id=".$sql->getValue("id")."&function=online_article&category_id=$category_id><font color=#dd0000>".$I18N->msg("status_offline")."</font></a>"; }elseif( $sql->getValue("status") == 1){ $article_status = "<a href=index.php?page=structure&article_id=".$sql->getValue("id")."&function=offline_article&category_id=$category_id><font color=#00dd00>".$I18N->msg("status_online")."</font></a>"; }

				echo "	<td class=grey><a href=index.php?page=structure&article_id=".$sql->getValue("id")."&function=delete_article&category_id=$category_id>".$I18N->msg("delete")."</a></td>
					<td class=grey>$article_status</td>";
			}
			echo "</tr>";

		}elseif($REX_USER->isValueOf("rights","article[".$sql->getValue("id")."]") || $REX_USER->isValueOf("rights","article[all]"))
		{
			echo "	<tr>
				<td class=grey align=center><a href=index.php?page=content&article_id=".$sql->getValue("id")."&category_id=$category_id&mode=edit><img src=pics/$icon width=16 height=16 border=0></a></td>
				<td class=grey><a href=index.php?page=content&article_id=".$sql->getValue("id")."&category_id=$category_id&mode=edit>".$sql->getValue("name")."&nbsp;</a></td>
				<td class=grey>$pos</td>
				<td class=grey>".$TEMPLATE_NAME[$sql->getValue("template_id")]."</td>
				<td class=grey>".date_from_mydate($sql->getValue("erstelldatum"),"")."&nbsp;</td>
				<td class=grey><b>$startpage</b></td>
				<td class=grey><strike>ändern</strike></td>";

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
			echo "	<tr>
				<td class=grey align=center><img src=pics/$icon width=16 height=16 border=0 align=middle></td>
				<td class=grey>".$sql->getValue("name")."</td>
				<td class=grey>$pos</td>
				<td class=grey>".$TEMPLATE_NAME[$sql->getValue("template_id")]."</td>
				<td class=grey>".date_from_mydate($sql->getValue("erstelldatum"),"")."&nbsp;</td>
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
