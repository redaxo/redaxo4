<?php

/**
* todos: regelt die Rechte an den einzelnen Kategorien und gibt den Pfad aus
* Kategorien = Startartikel und Bezüge
*
**/

$KATebene = 0; // aktuelle Ebene: default
$KatMaxEbenen = 6; // Maximale Unterebenen
$KATPATH = "|"; // Standard für path eintragungen in db

$KAT = new sql;
$KAT->setQuery("select * from rex_article where id=$category_id and startpage=1 and ctype=$ctype");

if ($KAT->getRows()==1)
{
	$KPATH = explode("|",$KAT->getValue("path"));
	$KATebene = count($KPATH)-1;
	for ($ii=1;$ii<$KATebene;$ii++)
	{
		$SKAT = new sql;
		$SKAT->setQuery("select * from rex_article where id=".$KPATH[$ii]." and startpage=1 and ctype=$ctype");
		$KATout .= " : <a href=index.php?page=structure&category_id=".$SKAT->getValue("id")."&ctype=$ctype>".$SKAT->getValue("name")."</a>";
		$KATPATH .= $KPATH[$ii]."|";
	}
	$KATout .= " : <a href=index.php?page=structure&category_id=$category_id&ctype=$ctype>".$KAT->getValue("name")."</a>";
	$KATPATH .= "$category_id|";
}
$KATout = "&nbsp;&nbsp;&nbsp;".$I18N->msg("path")." : <a href=index.php?page=structure&category_id=0&ctype=$ctype>Homepage</a>".$KATout;

// ***** aktuellen Artikel anzeigen

if ($article_id > 0 and $page == "content")
{
	if ($article->getValue("startpage")==1) $KATout .= " <br>&nbsp;&nbsp;&nbsp;".$I18N->msg("start_article")." : ";
	else $KATout .= " <br>&nbsp;&nbsp;&nbsp;".$I18N->msg("article")." : ";
	$KATout .= "<a href=index.php?page=content&article_id=$article_id&mode=edit&ctype=$ctype>".str_replace(" ","&nbsp;",$article->getValue("name"))."</a>";
	// $KATout .= " [$article_id]";
}



?>