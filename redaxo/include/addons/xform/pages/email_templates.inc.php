<?php

$SF = true;

$table = "rex_xform_email_template";
$bezeichner = "E-Mail Template";
$csuchfelder = array("name","mail_from","mail_subject","body");

$func = rex_request("func","string","");


//------------------------------> Hinzufügen

if($func == "add")
{
	echo $back_to_overview;
	$mita = new rexform;
	$mita->setWidth(770);
	$mita->setLabelWidth(160);
	$mita->setTablename($table);
	$oid = (int) $_REQUEST["oid"];
	$mita->setFormtype("add");
	$mita->setFormheader('
		<input type=hidden name=page value="'.$page.'" / />
		<input type=hidden name=subpage value="'.$subpage.'" />
		<input type=hidden name=func value="'.$func.'" />
		');
	$mita->setShowFormAlways(false);
	$mita->setValue("subline","$bezeichner erstellen" ,"left",0);

	$mita->setValue("text","Key - bitte nicht verändern","name",1);

	$mita->setValue("text","E-Mail [z.b.info@redaxo.de]","mail_from",1);
	$mita->setValue("text","E-Mail-Name [z.b. REDAXO Server]","mail_from_name",1);

	$mita->setValue("text","Betreff","subject",1);
	$mita->setValue("textarea","Body","body",1);

	echo $mita->showForm();

	if (!$mita->form_show)
	{
		$func = "";
	}
	
}



//------------------------------> Editieren
if($func == "edit")
{
	echo $back_to_overview;	
	$mita = new rexform;
	$mita->setWidth(770);
	$mita->setLabelWidth(160);
	$mita->setTablename($table);		
	$mita->setFormtype("edit", "id='".$oid."'", "Nachricht wurde nicht gefunden");

	$mita->setFormheader('
		<input type="hidden" name="page" value="'.$page.'" />
		<input type="hidden" name="subpage" value="'.$subpage.'" />
		<input type="hidden" name="func" value="'.$func.'" />
		<input type="hidden" name="oid" value="'.$oid.'" />
		');

	$mita->setShowFormAlways(false);				
	$mita->setValue("subline","$bezeichner edieren" ,"left",0);

	$mita->setValue("text","Key - bitte nicht verändern","name",1);

	$mita->setValue("text","E-Mail [z.b.info@redaxo.de]","mail_from",1);
	$mita->setValue("text","E-Mail-Name [z.b. REDAXO Server]","mail_from_name",1);

	$mita->setValue("text","Betreff","subject",1);
	$mita->setValue("textarea","Body","body",1);

	echo $mita->showForm();

	if (!$mita->form_show)
	{
		$func = "";
	}
	
}

//------------------------------> Löschen
if($func == "delete")
{
	$query = "delete from $table where id='".$oid."' ";
	$delsql = new rex_sql;
	$delsql->debugsql=0;
	$delsql->setQuery($query);
	$func = "";
}



//------------------------------> Liste
if($func == ""){
	
	
	/** Suche  **/
	$add_sql = "";
	$link	= "";
	
	// ADD
	echo '<table class="rex-table"><tr><td><a href="index.php?page='.$page.'&subpage='.$subpage.'&func=add"><b>+ '.$bezeichner.' hinzufügen</b></a></td></tr></table><br />';
	
	$sql = "select * from $table ".$add_sql;
	
	//echo $sql;
	
	$mit = new rexlist;
	$mit->setQuery($sql);
	$mit->setList(50);
	$mit->setGlobalLink("index.php?page=".$page."&subpage=".$subpage."".$link."&next=");

	$mit->setValue("id","id");

	$mit->setValue("Name/Key","name");
	$mit->setLink("index.php?page=".$page."&subpage=".$subpage."&func=edit&oid=","id");
	$mit->setValueOrder(1);

	$mit->setValue("Email von","mail_from");
	$mit->setLink("index.php?page=".$page."&subpage=".$subpage."&func=edit&oid=","id");
	$mit->setValueOrder(1);

	$mit->setValue("Betreff","subject");
	$mit->setLink("index.php?page=".$page."&subpage=".$subpage."&func=edit&oid=","id");
	$mit->setValueOrder(1);

	$mit->setValue("editieren","");
	$mit->setFormat("ifempty", "editieren");
	$mit->setLink("index.php?page=".$page."&subpage=".$subpage."&func=edit&oid=","id");
	
	$mit->setValue("löschen","");
	$mit->setFormat("ifempty", "löschen");
	$mit->setFormat("link","index.php?page=".$page."&subpage=".$subpage."&func=delete&oid=","id","", " onclick=\"return confirm('sicher löschen ?');\"");	
	
	if (isset($FORM["ordername"]) && isset($FORM["ordertype"])) $mit->setOrder($FORM["ordername"],$FORM["ordertype"]);

	$next = rex_request("next","int",0);
	echo $mit->showall($next);

}


?>