<?php

// ************************* TABELLE


$table = $REX['TABLE_PREFIX'].'em_table';
$table_field = $REX['TABLE_PREFIX'].'em_field';

$bezeichner = "Tabelle";

$func = rex_request("func","string","");
$page = rex_request("page","string","");
$subpage = rex_request("subpage","string","");
$table_id = rex_request("table_id","int");

if($func == "update")
{
	rex_em_generateAll();
	echo rex_info("Tabelle und Felder wurden erstellt und/oder aktualisiert");
	$func = "";
}



//------------------------------
if($func == "add" || $func == "edit")
{
	
	if($func == "edit")
		echo '<div class="rex-area"><h3 class="rex-hl2">Tabelle editieren</h3><div class="rex-area-content">';
	else
		echo '<div class="rex-area"><h3 class="rex-hl2">Tabelle hinzufügen</h3><div class="rex-area-content">';
		
	$xform = new rex_xform;
	
	$xform->setHiddenField("page",$page);
	$xform->setHiddenField("subpage",$subpage);
	$xform->setHiddenField("func",$func);
	
	// $xform->setDebug(TRUE);
	$xform->setActionField("showtext",array("","Vielen Dank fŸr die Eintragung"));
	
	$xform->setObjectparams("main_table",$table); // für db speicherungen und unique abfragen

	if($func == "edit")
	{
    $xform->setValueField("showvalue",array("label","Label"));
		$xform->setHiddenField("table_id",$table_id);
		$xform->setActionField("db",array($table,"id=$table_id"));
		$xform->setObjectparams("main_id",$table_id);
		$xform->setObjectparams("main_where","id=$table_id");
		$xform->setGetdata(true); // Datein vorher auslesen
	}elseif($func == "add")
	{
    $xform->setValueField("text",array("label","Label"));
    $xform->setValidateField("notEmpty",array("label","Bitte tragen Sie das Label ein"));
    $xform->setValidateField("preg_match",array("label","/[a-z_]*/i","Bitte tragen Sie beim Label nur Buchstaben ein"));
    $xform->setValidateField("customfunction",array("label","rex_em_checkLabelInTable","","Dieses Label ist bereits vorhanden"));
		$xform->setActionField("db",array($table));
	}
	
	$xform->setValueField("text",array("name","Name"));
	$xform->setValueField("textarea",array("description","Beschreibung"));
	$xform->setValueField("checkbox",array("status","Aktiv"));
	
	$xform->setValueField("validate",array("empty","name","Bitte den Namen eingeben"));
	
	echo $xform->getForm();

	echo '</div></div>';
	
	echo '<br />&nbsp;<br /><table cellpadding="5" class="rex-table"><tr><td><a href="index.php?page='.$page.'&amp;subpage='.$subpage.'"><b>&laquo; '.$I18N->msg('back_to_overview').'</b></a></td></tr></table>';
	
}






//------------------------------> Löschen
if($func == "delete"){
	$query = "delete from $table where id='".$table_id."' ";
	$delsql = new rex_sql;
	// $delsql->debugsql=1;
	$delsql->setQuery($query);
  $query = "delete from $table_field where table_id='".$table_id."' ";
	$delsql->setQuery($query);
  
	$func = "";
	echo rex_info($bezeichner." wurde gel&ouml;scht");
}


//------------------------------> Liste
if($func == ""){
	
	echo "<table cellpadding=5 class=rex-table><tr><td>
		<a href=index.php?page=".$page."&subpage=".$subpage."&func=add><b>+ $bezeichner anlegen</b></a>
		 | 
		<a href=index.php?page=".$page."&subpage=".$subpage."&func=update><b>Tabellen und Felder updaten</b></a>
		
		</td></tr></table><br />";
	
	$sql = "select * from $table order by name";

	$list = rex_list::factory($sql,30);
	$list->setColumnFormat('id', 'Id');

	// $list->setColumnParams("id", array("table_id"=>"###id###","func"=>"edit"));
	$list->removeColumn("id");
	
	$list->setColumnParams("label", array("table_id"=>"###id###","func"=>"edit"));
	
	$list->addColumn('Felder_editieren','Felder editieren');
	$list->setColumnParams("Felder_editieren", array("subpage"=>"field","table_id"=>"###id###"));

	$list->addColumn('l&ouml;schen','l&ouml;schen');
	$list->setColumnParams("l&ouml;schen", array("table_id"=>"###id###","func"=>"delete"));

	echo $list->get();

}