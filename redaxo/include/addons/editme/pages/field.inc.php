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
$field_id = rex_request("field_id","int");

$TYPE = array('value'=>"Werte",'validate'=>"Validierung/Überprüfung",'action'=>"Aktionen");

$tb = new rex_sql();
// $tb->debugsql = 1;
$tb->setQuery('select * from rex_em_table where id='.$table_id);
if($tb->getRows()==0)
{
	echo rex_warning('Diese Tabelle existiert nicht!');
	echo '<br />
	 <table cellpadding="5" class="rex-table">
	 <tr>
	   <td><a href="index.php?page='.$page.'&amp;subpage="><b>&laquo; '.$I18N->msg('back_to_overview').'</b></a></td>
	 </tr>
	 </table>';
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
					echo '<p class="rex-button"><a class="rex-button" href="'.$link.'type_id=validate&type_name='.$k.'">'.$k.'</a> '.$v['description'].'</p>';
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

if( 
    ($func == "add" || $func == "edit" )  && 
    isset($types[$type_id][$type_name]) 
  )
{
	
	
	if($func == "add")
	 echo '<div class="rex-area"><h3 class="rex-hl2">'.$I18N->msg("editme_addfield").' "'. $type_name .'"</h3><div class="rex-area-content">';
	else
   echo '<div class="rex-area"><h3 class="rex-hl2">'.$I18N->msg("editme_editfield").' "'. $type_name .'"</h3><div class="rex-area-content">';
	
	$xform = new rex_xform;
  // $xform->setDebug(TRUE);
  
  $xform->setHiddenField("page",$page);
  $xform->setHiddenField("subpage",$subpage);
  $xform->setHiddenField("func",$func);
  
  $xform->setValueField("hidden",array("table_id",$table_id,"REQUEST"));
  $xform->setValueField("hidden", array("type_name",$type_name,"REQUEST"));
  $xform->setValueField("hidden", array("type_id",$type_id,"REQUEST"));
  
  $xform->setValueField("text",array("prio","Prioritaet"));
  
  
	$i = 0;
	foreach($types[$type_id][$type_name]['values'] as $v)
	{
		$i++;

		switch($v['type'])
		{

			case("label"):

				if($func == "edit" )
				{
					$xform->setValueField("showvalue",array("f".$i,"Label"));
				}else
				{
					$xform->setValueField("text",array("f".$i,"Label"));
					$xform->setValidateField("notEmpty",array("f".$i,"Bitte tragen Sie das Label ein"));					
					$xform->setValidateField("preg_match",array("f".$i,"/[a-z_]*/i","Bitte tragen Sie beim Label nur Buchstaben ein"));					
					$xform->setValidateField("customfunction",array("f".$i,"rex_em_checkField",$table_id,"Dieses Label ist bereits vorhanden"));					
				}
				break;
			
      case("no_db"):
				$xform->setValueField("checkbox",array("f".$i,"Nicht in Datenbank speichern",1,0));
        break;

      case("boolean"):
      	// checkbox|check_design|Bezeichnung|Value|1/0|[no_db]
				$xform->setValueField("checkbox",array("f".$i,$v['name'])); 
      	break;
        
      case("getlabel"):
      	// Todo:
      	
      case("getlabels"):
        // Todo:

      default:
				$xform->setValueField("text",array("f".$i,$v['name'])); 
				
		}
		
	}

	$xform->setActionField("showtext",array("","<p>Vielen Dank für die Eintragung</p>"));	
	$xform->setObjectparams("main_table",$table); // für db speicherungen und unique abfragen
	
	if($func == "edit")
	{
		$xform->setHiddenField("field_id",$field_id);
		$xform->setActionField("db",array($table,"id=$field_id"));	
		$xform->setObjectparams("main_id",$field_id);
		$xform->setObjectparams("main_where","id=$field_id");
		$xform->setGetdata(true); // Datein vorher auslesen
	}elseif($func == "add")
	{
		$xform->setActionField("db",array($table));
	}
	
	if($type_id == "value")
	{
		$xform->setValueField("checkbox",array("list_hidden","In Liste verstecken",1,"0"));
	}else	if($type_id == "validate")
	{
		$xform->setValueField("hidden",array("list_hidden",1));
	}
	echo $xform->getForm();

	echo '</div></div>';
	
	echo '<br />&nbsp;<br /><table cellpadding="5" class="rex-table"><tr><td><a href="index.php?page='.$page.'&amp;subpage='.$subpage.'&amp;table_id='.$table_id.'"><b>&laquo; '.$I18N->msg('back_to_overview').'</b></a></td></tr></table>';
	
}






//------------------------------> Löschen
if($func == "delete"){
	$query = 'delete from '.$table.' where table_id='.$table_id.' and id='.$field_id;
	$delsql = new rex_sql;
	// $delsql->debugsql=1;
	$delsql->setQuery($query);
	$func = "";
	echo rex_info($bezeichner." wurde gel&ouml;scht");
}


//------------------------------> Liste
if($func == ""){
	
	echo '<table cellpadding=5 class=rex-table><tr><td><a href=index.php?page='.$page.'&subpage='.$subpage.'&table_id='.$table_id.'&func=choosenadd><b>+ '.$bezeichner.' anlegen</b></a></td></tr></table><br />';
	
	$sql = 'select * from '.$table.' where table_id='.$table_id.' order by prio';
	$list = rex_list::factory($sql,30);
	$list->setColumnFormat('id', 'Id');

	$list->addParam("page", $page);
	$list->addParam("subpage", $subpage);
	$list->addParam("table_id", $table_id);

  $list->removeColumn('table_id');
  $list->removeColumn('id');
  
  for($i=3;$i<10;$i++)
  {
    $list->removeColumn('f'.$i);
  }	
	
	// $list->setColumnFormat('type_id', 'Typ');
	// $list->setColumnFormat('field', 'Feld');

	$list->addColumn('editieren','Feld editieren');
	$list->setColumnParams("editieren", array("field_id"=>"###id###","func"=>"edit",'type_name'=>'###type_name###','type_id'=>'###type_id###',));

	$list->addColumn('l&ouml;schen','l&ouml;schen');
	$list->setColumnParams("l&ouml;schen", array("field_id"=>"###id###","func"=>"delete"));
	

	echo $list->get();

}

?>