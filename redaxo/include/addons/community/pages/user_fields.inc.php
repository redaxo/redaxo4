<?php

error_reporting(E_ALL);

$SF = true;

$table = 'rex_com_user_field';
$table_user = 'rex_com_user';
$bezeichner = "Userfeld";

$func = rex_request("func","string","");
$FORM = rex_request("FORM","array");


$rep = "";
$UT = $REX["ADDON"]["community"]["ut"];
foreach($UT as $key => $value)
{
	if ($rep != "") $rep .= "|";
	$rep .= "$key|[$key] $value";
}

//------------------------------> Poll Anlegen|Editieren
if($func == "add" || $func == "edit")
{
	
	$mita = new rexform;
	
	$mita->setWidth(770);
	$mita->setLabelWidth(160);
	$mita->setTablename($table);
	
	if($func == "add"){
		$mita->setFormtype("add");
		$mita->setFormheader("
			<input type=hidden name=page value=".$page." />
			<input type=hidden name=subpage value=".$subpage." />
			<input type=hidden name=func value=".$func." />");
		$mita->setShowFormAlways(false);
		$mita->setValue("subline","$bezeichner erstellen" ,"left",0);

		$mita->setValue("text","prior","prior",1);
		$mita->setValue("text","name","name",1);
		$mita->setValue("text","userfield","userfield",1);
		$mita->setValue("singleselect","type","type",1, $rep);
		$mita->setValue("text","extra1","extra1",0);
		// $mita->setValue("text","extra2","extra2",0);
		// $mita->setValue("text","extra3","extra3",0);
		$mita->setValue("checkbox","Erscheint in Userliste","inlist",0);
		$mita->setValue("checkbox","Editierbar","editable",0);
		$mita->setValue("checkbox","Pflichtfeld","mandatory",0);
		$mita->setValue("text","Defaultwert","defaultvalue",0);
		$mita->setValue("subline","***** Beispiele" ,"left",0);
		$mita->setValue("subline","INT * extra1=14" ,"left",0);
		$mita->setValue("subline","VARCHAR * extra1=255" ,"left",0);
		$mita->setValue("subline","TEXT * " ,"left",0);
		$mita->setValue("subline","PASSWORD * extra1=md5" ,"left",0);
		$mita->setValue("subline","SELECT * extra1 0=offline|1=online" ,"left",0);
		$mita->setValue("subline","BOOL	*" ,"left",0);

	}else{			
		$mita->setFormtype("edit", "id='".$oid."'", "$bezeichner wurde nicht gefunden");
		$mita->setFormheader("
			<input type=hidden name=page value=".$page.">
			<input type=hidden name=subpage value=".$subpage.">
			<input type=hidden name=func value=".$func." />
			<input type=hidden name=oid value=".$oid.">");
		$mita->setShowFormAlways(false);				
		$mita->setValue("subline","$bezeichner edieren" ,"left",0);
		$mita->setValue("text","prior","prior",1);
		$mita->setValue("text","name","name",1);
		$mita->setValue("showtext","userfield","userfield");
		$mita->setValue("checkbox","Erscheint in Userliste","inlist",0);
		$mita->setValue("checkbox","Editierbar","editable",0);
		$mita->setValue("checkbox","Pflichtfeld","mandatory",0);
		$mita->setValue("text","Defaultwert","defaultvalue",0);
		$mita->setValue("showtext","extra1","extra1",0);
		// $mita->setValue("showtext","extra2","extra2",0);
		// $mita->setValue("showtext","extra3","extra3",0);

	}

	echo $mita->showForm();

	if (!$mita->form_show)
	{
		$func = "";
		echo "<br />";
	}
	else echo "<br /><table class=rex-table><tr><td><a href=index.php?page=".$page."&subpage=".$subpage."><b>&laquo; Zurück zur Übersicht</b></a></td></tr></table>";
	
}

//------------------------------> Partner löschen
if($func == "delete"){

	$gf = new rex_sql;
	$gf->setQuery("select * from $table where id='".$oid."'");
	if ($gf->getRows()==1 && $gf->getValue("userfield")!= "id")
	{
		// feste felder - nicht loeschbar	
		if (in_array($gf->getValue("userfield"),$REX["ADDON"]["community"]["ff"]))
		{
			
			echo "<p class=rex-warning>Das Feld <b>".$gf->getValue("userfield")."</b> kann nicht gelöscht werden da es ein fester Bestandteil ist</p><p class=rex-clear></p>";
			$func = "";	
			
		}else
		{
			$query = "delete from $table where id='".$oid."' ";
			$delsql = new rex_sql;
			$delsql->debugsql=0;
			$delsql->setQuery($query);
			$func = "";
			$gf->setQuery("ALTER TABLE `$table_user` DROP `".$gf->getValue("userfield")."`");
		}
	}
}



//------------------------------> Userliste
if($func == ""){

	// ***** add 
	echo "<table cellpadding=5 class=rex-table><tr><td><a href=index.php?page=".$page."&subpage=".$subpage."&func=add><b>+ $bezeichner anlegen</b></a></td></tr></table><br />";


	// ***** Suche
	$addsql = "";
	$link	= "";
	$ssql 	= new rex_sql();
	
	$sql = "select * from $table order by prior";
	
	$mit = new rexlist;
	$mit->setQuery($sql);
	$mit->setList(50);
	$mit->setGlobalLink("index.php?page=".$page."&subpage=".$subpage."".$link."&next=");
	$mit->setValue("prior","prior");
	$mit->setValue("id","id");
	$mit->setValue("Name","name");
	$mit->setValueOrder(1);
	$mit->setLink("index.php?page=".$page."&subpage=".$subpage."&func=edit&oid=","id");
	$mit->setValue("Userfield","userfield");

	$mit->setValue("Typ","type");
	$mit->setFormat("replace_value",$rep);

	$mit->setValue("In Übersicht","inlist");
	$mit->setFormat("replace_value","0|nein|1|ja");
	
	$mit->setValue("Editierbar","editable");
	$mit->setFormat("replace_value","0|nein|1|ja");
	
	$mit->setValue("Pflichtfeld","mandatory");
	$mit->setFormat("replace_value","0|nein|1|ja");

	$mit->setValue("löschen","");
	$mit->setFormat("ifempty", "- löschen");
	$mit->setFormat("link","index.php?page=".$page."&subpage=".$subpage."&func=delete&oid=","id",""," onclick=\"return confirm('sicher löschen ?');\"");	
	if (isset($FORM["ordername"]) && isset($FORM["ordertype"])) $mit->setOrder($FORM["ordername"],$FORM["ordertype"]);
	echo $mit->showall(@$next);
	
	
	
	// **************** bei jedem Aufruf Felder abgleichen
	
	$err_msg = array();
	
	$guf = new rex_sql;
	$guf->setQuery("select * from ".$table." order by prior");
	$fields = array();
	$gufa = $guf->getArray();
	foreach($gufa as $key => $value)
	{
	  $userfield = $value["userfield"];
	  $fields[$userfield] = $value["type"];
	  $extra1[$userfield] = $value["extra1"];
	  $extra2[$userfield] = $value["extra2"];
	  $extra3[$userfield] = $value["extra3"];
	  $utype[$userfield] = $value["type"];
	  // echo "<br />$key - $userfield - ".$value["type"];
	}
	
	// $UT - Feldtypen drin..
	$gu = new rex_sql;
	$gu->setQuery("SHOW COLUMNS from ".$table_user);
	foreach($gu->getArray() as $key => $value)
	{
		$field = $value["Field"];
		$type = $value["Type"];
		// echo "<br />$key - ".$value["Field"]." - ".$value["Type"]." - ".$value["Extra"];
		if ($field=="id") echo ""; // ID wird ignoriert
		elseif (@$fields[$field] != "") echo ""; // Feld vorhanden - alles ist ok
		else {
			// Feld zuviel - Melden
			$err_msg[] = "In der Usertabelle ist folgendes Feld zuviel: <b>$field | $type</b>. Bitte nachträglich hier anlegen.";
		}
		$ufields[$field] = $type;
	}
	
	foreach($fields as $field => $value)
	{
		if (isset($ufields[$field]) &&  $ufields[$field] != "") echo ""; // Feld vorhanden - alles ist gut
		else
		{
			// Feld fehlt -> anlegen
			$err_msg[] = rex_com_utcreate($table_user,$field,$utype[$field],$extra1[$field],$extra2[$field],$extra3[$field]);
		}
	}
	
	echo "<br />";
	
	foreach($err_msg as $key => $value)
	{
		echo rex_warning($value);
	}
	
	if (count($err_msg)==0) echo rex_info("Alle Felder wurden überprüft und es wurden keine Fehler gefunden.");

}

?>