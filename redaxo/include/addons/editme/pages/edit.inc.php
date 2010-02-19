<?php

// ********************************************* DATA ADD/EDIT/LIST

$func = rex_request("func","string","");
$data_id = rex_request("data_id","int","");
$rex_em_opener_field = rex_request("rex_em_opener_field","int","");
$rex_em_opener_fieldname = rex_request("rex_em_opener_fieldname","string","");


$show_list = TRUE;

foreach($tables as $table)
{
	$name = $table['name'];
	$id = $table['id'];
	$table["tablename"] = 'rex_em_data_'.$table['name'];

	if($subpage == $table['name'])
	{
		echo '<table cellpadding="5" class="rex-table"><tr><td><b>'.$table["label"].'</b> - '.$table["description"].'</td></tr></table><br />';
		break; // Wenn Tabelle gefunden - abbrechen
	}

}

/*
 * 
 * POPUP SACHEN
rex_set_session('media[opener_input_field]', $opener_input_field);
$opener_link = rex_request('opener_link', 'string');
 */

if($rex_em_opener_field != "")
{
	echo '<link rel="stylesheet" type="text/css" href="../files/addons/editme/popup.css" media="screen, projection, print" />';
}







// ********************************************* LOESCHEN
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





// ********************************************* FORMULAR
$fields = rex_em_getFields($table['name']);
if($func == "add" || $func == "edit")
{
	
	$xform = new rex_xform;
	// $xform->setDebug(TRUE);
	$xform->setHiddenField("page",$page);
	$xform->setHiddenField("subpage",$subpage);
	$xform->setHiddenField("func",$func);
	$xform->setHiddenField("rex_em_opener_field",$rex_em_opener_field);
	$xform->setHiddenField("rex_em_opener_fieldname",$rex_em_opener_fieldname);
	
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
		
	// $xform->setActionField("showtext",array("","Vielen Dank fŸr die Eintragung"));
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

  $form = $xform->getForm();
  
  if($xform->objparams["form_show"])
  {
	  if($func == "edit")
	    echo '<div class="rex-area"><h3 class="rex-hl2">Daten editieren</h3><div class="rex-area-content">';
	  else
	    echo '<div class="rex-area"><h3 class="rex-hl2">Datensatz anlegen</h3><div class="rex-area-content">';
	  echo $form;
    echo '</div></div>';
    echo '<br />&nbsp;<br /><table cellpadding="5" class="rex-table"><tr><td><a href="index.php?page='.$page.'&amp;subpage='.$subpage.'&rex_em_opener_field='.$rex_em_opener_field.'&rex_em_opener_fieldname='.htmlspecialchars($rex_em_opener_fieldname).'"><b>&laquo; '.$I18N->msg('back_to_overview').'</b></a></td></tr></table>';
    $show_list = FALSE;
  }else
  {
    if($func == "edit")
      echo rex_info("Vielen Dank f&uuml;r die Aktualisierung.");
    elseif($func == "add")
      echo rex_info("Vielen Dank f&uuml;r den Eintrag.");
  }
	
}





// ********************************************* LIST
if($show_list)
{
	echo '<table cellpadding="5" class="rex-table"><tr><td><a href="index.php?page='.$page.'&subpage='.$subpage.'&func=add&rex_em_opener_field='.$rex_em_opener_field.'&rex_em_opener_fieldname='.htmlspecialchars($rex_em_opener_fieldname).'"><b>+ anlegen</b></a></td></tr></table><br />';


	$sql = "select * from ".$table["tablename"];

	$list = rex_list::factory($sql,30);
	$list->setColumnFormat('id', 'Id');

	$list->setColumnParams("id", array("data_id"=>"###id###","func"=>"edit","rex_em_opener_field"=>$rex_em_opener_field,"rex_em_opener_fieldname"=>$rex_em_opener_fieldname));
	// $list->setColumnParams("login", array("table_id"=>"###id###","func"=>"edit"));
	// $list->removeColumn("id");
	
	$fields = rex_em_getFields($table['name']);
	foreach($fields as $field)
    {
  	  if($field["type_id"] == "value")
 	  {
        if($field["list_hidden"] == 1)
        {
          $list->removeColumn($field["f1"]);
        }
	  }
  	}
	
	$list->addColumn('editieren','editieren');
	$list->setColumnParams("editieren", array("data_id"=>"###id###","func"=>"edit","rex_em_opener_field"=>$rex_em_opener_field,"rex_em_opener_fieldname"=>$rex_em_opener_fieldname));

	$list->addColumn('l&ouml;schen','l&ouml;schen');
	$list->setColumnParams("l&ouml;schen", array("data_id"=>"###id###","func"=>"delete","rex_em_opener_field"=>$rex_em_opener_field,"rex_em_opener_fieldname"=>$rex_em_opener_fieldname));

	if($rex_em_opener_field)
	{
		$list->addColumn('&uuml;bernehmen','<a href="javascript:em_setData('.$rex_em_opener_field.',###id###,\'###'.$rex_em_opener_fieldname.'###\')">&uuml;bernehmen</a>',-1,"sdfghjkl");
		// $list->setColumnParams("&uuml;bernehmen", array("data_id"=>"###id###","func"=>"","rex_em_opener_field"=>$rex_em_opener_field));
	}
	
	echo $list->get();
	
}
