<?php

/**
* todos: regelt die Rechte an den einzelnen Kategorien und gibt den Pfad aus
* Kategorien = Startartikel und Bezüge
*
**/

$KATebene = 0; // aktuelle Ebene: default
$KATPATH = "|"; // Standard für path eintragungen in db

$KATPERM = false;
if ($REX_USER->isValueOf("rights","csw[0]") || $REX_USER->isValueOf("rights","admin[]") || $REX_USER->isValueOf("rights","dev[]")) $KATPERM = true;

$KAT = new sql;
$KAT->setQuery("select * from rex_article where id=$category_id and startpage=1 and clang=$clang");

if ($KAT->getRows()!=1)
{
	// kategorie existiert nicht
	
}else
{
	// kategorie existiert
	
	$KPATH = explode("|",$KAT->getValue("path"));
	$KATebene = count($KPATH)-1;
	for ($ii=1;$ii<$KATebene;$ii++)
	{
		
		$SKAT = new sql;
		$SKAT->setQuery("select * from rex_article where id=".$KPATH[$ii]." and startpage=1 and clang=$clang");

		if ($SKAT->getRows()==1)
		{

			if ($REX_USER->isValueOf("rights","csw[".$SKAT->getValue("id")."]"))
			{

				$KATout .= " : <a href=index.php?page=structure&category_id=".$SKAT->getValue("id")."&clang=$clang>".$SKAT->getValue("catname")."</a>";
				$KATPATH .= $KPATH[$ii]."|";
				$KATPERM = true;

			}else if ($REX_USER->isValueOf("rights","csr[".$SKAT->getValue("id")."]"))
			{

				$KATout .= " : <a href=index.php?page=structure&category_id=".$SKAT->getValue("id")."&clang=$clang>".$SKAT->getValue("catname")."</a>";
				$KATPATH .= $KPATH[$ii]."|";

			}

		}

	}
	
	if ($KATPERM || $REX_USER->isValueOf("rights","csr[$category_id]") || $REX_USER->isValueOf("rights","csw[$category_id]"))
	{

		$KATout .= " : <a href=index.php?page=structure&category_id=$category_id&clang=$clang>".$KAT->getValue("catname")."</a>";
		$KATPATH .= "$category_id|";
		if ($REX_USER->isValueOf("rights","csw[$category_id]")) $KATPERM = true;

	}else
	{
		$category_id = 0;	
		$article_id = 0;
	}

}

$KATout = "&nbsp;&nbsp;&nbsp;".$I18N->msg("path")." : <a href=index.php?page=structure&category_id=0&clang=$clang>Homepage</a>".$KATout;

?>