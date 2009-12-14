<?php

// Hier werden die einzelnen Datensaetze angezeigt 
// und entsprechend der Konfiguration verwaltbar gemacht

$func = rex_request("func","string","");
$data_id = rex_request("data_id","int","");

foreach($tables as $table)
{
	$name = $table['name'];
	$id = $table['id'];
	$table["tablename"] = 'rex_em_data_'.$table['label'];
	 
	if($subpage == $table['label'])
	{
		echo '<br /><table cellpadding="5" class="rex-table"><tr><td><b>'.$table["name"].'</b> - '.$table["description"].'</td></tr></table><br />';
		break; // Wenn Tabelle gefunden - abbrechen
	}

}


if($func == "delete")
{
  $query = 'delete from '.$table["tablename"].' where id='.$data_id;
  $delsql = new rex_sql;
  // $delsql->debugsql=1;
  $delsql->setQuery($query);
  $func = "";
  echo rex_info("Datensatz wurde gel&ouml;scht");
	
	
  $func = "";
}


$fields = rex_em_getFields($table['id']);
	

//------------------------------ Add und Edit
if($func == "add" || $func == "edit")
{
	
	if($func == "edit")
		echo '<div class="rex-area"><h3 class="rex-hl2">Daten editieren</h3><div class="rex-area-content">';
	else
		echo '<div class="rex-area"><h3 class="rex-hl2">Datensatz anlegen</h3><div class="rex-area-content">';
		
	$xform = new rex_xform;
	$xform->setDebug(TRUE);
	$xform->setHiddenField("page",$page);
	$xform->setHiddenField("subpage",$subpage);
	$xform->setHiddenField("func",$func);

	foreach($fields as $field)
	{
		$type_name = $field["type_name"];
		$type_id = $field["type_id"];

		$values = array();
    for($i=1;$i<10;$i++){ $values[] = $field["f".$i]; }
    
		if($type_id == "value")
			$xform->setValueField($field["type_name"],$values);
		elseif($type_id == "validate")
			$xform->setValidateField($field["type_name"],$values);
		elseif($type_id == "action")
			$xform->setActionField($field["type_name"],$values);
	}
		
	$xform->setActionField("showtext",array("","Vielen Dank fŸr die Eintragung"));
	$xform->setObjectparams("main_table",$table["tablename"]); // für db speicherungen und unique abfragen

	if($func == "edit")
	{
		$xform->setHiddenField("data_id",$data_id);
		$xform->setActionField("db",array($table["tablename"],"id=$data_id"));
		$xform->setObjectparams("main_id",$data_id);
		$xform->setObjectparams("main_where","id=$data_id");
		$xform->setGetdata(true); // Datein vorher auslesen
	}elseif($func == "add")
	{
		$xform->setActionField("db",array($table["tablename"]));
	}
  
  echo $xform->getForm();

	echo '</div></div>';
	
	echo '<br />&nbsp;<br /><table cellpadding="5" class="rex-table"><tr><td><a href="index.php?page='.$page.'&amp;subpage='.$subpage.'"><b>&laquo; '.$I18N->msg('back_to_overview').'</b></a></td></tr></table>';
	
}else
{


	//------------------------------  Datensaetze anzeigen

	echo "<table cellpadding=5 class=rex-table><tr><td><a href=index.php?page=".$page."&subpage=".$subpage."&func=add><b>+ anlegen</b></a></td></tr></table><br />";
		 
	$fields = rex_em_getFields($table['id']);

	$sql = "select * from ".$table["tablename"];

	$list = rex_list::factory($sql,30);
	$list->setColumnFormat('id', 'Id');

	$list->setColumnParams("id", array("table_id"=>"###id###","func"=>"edit"));
	// $list->setColumnParams("login", array("table_id"=>"###id###","func"=>"edit"));

	// $list->removeColumn("id");
	
	foreach($fields as $field)
  {
  	if($field["list_hidden"] == 1)
  	 $list->removeColumn($field["f1"]);
  }
	
	
	$list->addColumn('editieren','editieren');
	$list->setColumnParams("editieren", array("data_id"=>"###id###","func"=>"edit"));

	$list->addColumn('l&ouml;schen','l&ouml;schen');
	$list->setColumnParams("l&ouml;schen", array("data_id"=>"###id###","func"=>"delete"));

	echo $list->get();
	
}







