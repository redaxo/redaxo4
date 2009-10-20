<?php

// ************************* FELDER EINER TABELLE

$table = $REX['TABLE_PREFIX'].'em_field';

$bezeichner = "Tabellenfeld";

$func = rex_request("func","string","");
$page = rex_request("page","string","");
$subpage = rex_request("subpage","string","");
$table_id = rex_request("table_id","int");
$type_id = rex_request("type_id","string");
$type_name = rex_request("type_name","string");

$TYPE = array('value'=>"Werte",'validate'=>"Validierung/Überprüfung",'action'=>"Aktionen");

$tb = new rex_sql();
// $tb->debugsql = 1;
$tb->setQuery('select * from rex_em_table where id='.$table_id);
if($tb->getRows()==0)
{
	echo rex_warning('Diese Tabelle existiert nicht!');
	echo '<br /><table cellpadding="5" class="rex-table"><tr><td><a href="index.php?page='.$page.'&amp;subpage="><b>&laquo; '.$I18N->msg('back_to_overview').'</b></a></td></tr></table>';
	$func = "nothing";
}else
{
	echo '<br /><table cellpadding="5" class="rex-table"><tr><td><b>'.$tb->getValue("name").'</b> - '.$tb->getValue("description").'</td></tr></table><br />';
}






$types = rex_xform::getTypeArray();



//------------------------------

if($func == "choosenadd")
{

	// type and choose !!
	
	$link = 'index.php?page=editme&subpage=field&table_id='.$table_id.'&func=add&';
	?>	

	<div class="rex-addon-output">
		<h2 class="rex-hl2"><?php echo $I18N->msg('editme_choosenadd'); ?></h2>

		<div class="rex-addon-content">
			<p class="rex-tx1"><?php echo $I18N->msg('editme_choosenadd_description'); ?></p>
		</div>
	</div>

	<div class="rex-addon-output">
	<div class="rex-area-col-2">
		<div class="rex-area-col-a">
			<h3 class="rex-hl2"><?php echo $TYPE['value']; ?></h3>
			<div class="rex-area-content">
				<p class="rex-tx1"></p><?php
				
				if(isset($types['value']))
				foreach($types['value'] as $k => $v)
				{
					echo '<p class="rex-button"><a class="rex-button" href="'.$link.'type_id=value&type_name='.$k.'">'.$k.'</a> '.$v['description'].'</p>';
				}
				
				?></p>
			</div>
		</div>
		<div class="rex-area-col-b">
			<h3 class="rex-hl2"><?php echo $TYPE['validate']; ?></h3>
			<div class="rex-area-content">
				<p class="rex-tx1"><?php
				if(isset($types['validate']))
				foreach($types['validate'] as $k => $v)
				{
					echo '<p class="rex-button">"<a href="'.$link.'type_id=validate&type_name='.$k.'">'.$k.'</a>" - '.$v['description'].'</p>';
				}
				
				?></p>
			</div>
		</div>
	</div>
	</div>
	
	<div class="rex-addon-output">
		<h2 class="rex-hl2"><?php echo $TYPE['action']; ?></h2>
		<div class="rex-addon-content">
			<p class="rex-tx1"><?php
				if(isset($types['action']))
				foreach($types['action'] as $k => $v)
				{
					echo '<p class="rex-button">"<a href="'.$link.'type_id=action&type_name='.$k.'">'.$k.'</a>" - '.$v['description'].'</p>';
				}
				
				?></p>
		</div>
	</div>
	
	<?php	

}


//------------------------------

if($func == "add" && isset($types[$type_id][$type_name]))
{
	
	echo '<div class="rex-area"><h3 class="rex-hl2">'.$I18N->msg("editme_addfield").'</h3><div class="rex-area-content">';
		
	// ***** Allgemeine BE Felder reinlegen
	$form_data = "\n".'hidden|page|'.$page.'|REQUEST|no_db'."\n";
	$form_data.= 'hidden|subpage|'.$subpage.'|REQUEST|no_db'."\n";
  $form_data.= 'hidden|table_id|'.$table_id.'|REQUEST|'."\n";
  $form_data.= 'hidden|type_name|'.$type_name.'|REQUEST|'."\n";
  $form_data.= 'hidden|type_id|'.$type_id.'|REQUEST|'."\n";
  $form_data.= 'hidden|func|'.$func.'|REQUEST|no_db';
	
  /*
  */
	echo '<pre>';
	var_dump($types[$type_id][$type_name]['values']);
	echo '</pre>';
	
	$i = 0;
	foreach($types[$type_id][$type_name]['values'] as $k => $v)
	{
		$i++;
		switch($k)
		{
			
			case("label"):
        $form_data .= "\ntext|f".$i."|".$v[0];
        $form_data .= "\nvalidate|notEmpty|f".$i."|Bitte tragen Sie das Label ein";
				break;
			
      case("no_db"):
        $form_data .= "\ncheckbox|f".$i."|Nicht in Datenbank speichern"."|no_db|".$v[1];
      	break;

      case("boolean"):
        $form_data .= "\ncheckbox|f".$i."|".$v[0]."|";    // checkbox|check_design|Bezeichnung|Value|1/0|[no_db]
      	break;
        
			default:
        $form_data .= "\ntext|f".$i."|".$v[0];
				
		}
		
	}

	// $form_data.= "\n".'text|name|Name|';
	// $form_data.= "\n".'textarea|description|Beschreibung|';
	// $form_data.= "\n".'validate|empty|name|Bitte den Namen eingeben';

	$form_data = trim(str_replace("<br />","",rex_xform::unhtmlentities($form_data)));

	$xform = new rex_xform;
  $xform->setDebug(TRUE);
	$xform->objparams["actions"][] = array("type" => "showtext","elements" => array("action","showtext",'','<p>Vielen Dank für die Eintragung</p>',"",),);
	$xform->setObjectparams("main_table",$table); // für db speicherungen und unique abfragen

	
	if($func == "edit")
	{
		$form_data .= "\n".'hidden|table_id|'.$table_id.'|REQUEST|no_db';
		$xform->objparams["actions"][] = array("type" => "db","elements" => array("action","db",$table,"id=$table_id"),);

		$xform->setObjectparams("main_id","$table_id");
		$xform->setObjectparams("main_where","id=$table_id");
		$xform->setGetdata(true); // Datein vorher auslesen
	}elseif($func == "add")
	{
		$xform->objparams["actions"][] = array("type" => "db","elements" => array("action","db",$table),);
	}

	$xform->setFormData($form_data);

	echo '<pre>'; var_dump($xform->objparams); echo '</pre>';
	
	
	echo $xform->getForm();

	echo '</div></div>';
	
	echo '<br />&nbsp;<br /><table cellpadding="5" class="rex-table"><tr><td><a href="index.php?page='.$page.'&amp;subpage='.$subpage.'&amp;table_id='.$table_id.'"><b>&laquo; '.$I18N->msg('back_to_overview').'</b></a></td></tr></table>';
	
}






//------------------------------> Löschen
if($func == "delete"){
	/*
	$query = "delete from $table where id='".$table_id."' ";
	$delsql = new rex_sql;
	$delsql->debugsql=1;
	$delsql->setQuery($query);
	*/
	$func = "";
	echo rex_info($bezeichner." wurde gel&ouml;scht");
}


//------------------------------> Liste
if($func == ""){
	
	echo '<table cellpadding=5 class=rex-table><tr><td><a href=index.php?page='.$page.'&subpage='.$subpage.'&table_id='.$table_id.'&func=choosenadd><b>+ '.$bezeichner.' anlegen</b></a></td></tr></table><br />';
	
	$sql = 'select * from '.$table.' where table_id='.$table_id.'';
	$list = rex_list::factory($sql,30);
	$list->setColumnFormat('id', 'Id');

	$list->addParam("page", $page);
	$list->addParam("subpage", $subpage);
	$list->addParam("table_id", $table_id);

	// $list->setColumnFormat('type_id', 'Typ');
	// $list->setColumnFormat('field', 'Feld');

	$list->addColumn('editieren','Feld editieren');
	$list->setColumnParams("editieren", array("field_id"=>"###id###","func"=>"edit"));

	$list->addColumn('l&ouml;schen','l&ouml;schen');
	$list->setColumnParams("l&ouml;schen", array("field_id"=>"###id###","func"=>"delete"));
	

	echo $list->get();

}

?>