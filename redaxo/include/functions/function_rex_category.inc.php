<?
// changed 4.4.04 Carsten Eckelmann <careck@circle42.com>
$KAT = new sql;
$KATs = new sql;
$KATs->setQuery("select * from rex_category");
$KATlink = "index.php?page=structure";
$KATout = "";
$KATebene = 0;
$KATcategory_id = $category_id;
$KATSQLpath = "";
$KatMaxEbenen = 5;

for ($ii=0;$ii<$KatMaxEbenen;$ii++)
{
	$KATs->counter = 0;
	for($ik=0;$ik<$KATs->getRows();$ik++)
	{
		if ($KATcategory_id == $KATs->getValue("id"))
		{
			if ($REX_USER->isValueOf("rights","structure[$KATcategory_id]")) $STRUCTURE_PERM = TRUE;
			if ($REX_USER->isValueOf("rights","expertMode[]")) $add_on = " [".$KATs->getValue("id")."]";
			else $add_on = "";
			
			$KATout = " : <a href=index.php?page=structure&category_id=".$KATs->getValue("id").">".$KATs->getValue("name")."</a>$add_on".$KATout;
			
			$KATSQLpath = "-".$KATs->getValue("id").$KATSQLpath;
			
			$KATcategory_id = $KATs->getValue("re_category_id");

			if ($KATs->getValue("id") == $category_id) $re_category_id = $KATs->getValue("re_category_id");

			$KATebene++;
			break;
		}
		$KATs->next();	
	}
	if ($KATcategory_id == 0){ break; }
}

$KATout = "&nbsp;&nbsp;&nbsp;".$I18N->msg("path")." : <a href=$KATlink&category_id=0>Homepage</a> ".$KATout;

if ($article_id != "" and $page == "content")
{
	if ($article->getValue("startpage")==1) $KATout .= " <br>&nbsp;&nbsp;&nbsp;".$I18N->msg("start_article")." : ";
	else $KATout .= " <br>&nbsp;&nbsp;&nbsp;".$I18N->msg("article")." : ";
	$KATout .= "<a href=index.php?page=content&article_id=$article_id&mode=edit>".str_replace(" ","&nbsp;",$article->getValue("name"))."</a>";
	if ($REX_USER->isValueOf("rights","expertMode[]")) $KATout .= " [$article_id]";
}

?>