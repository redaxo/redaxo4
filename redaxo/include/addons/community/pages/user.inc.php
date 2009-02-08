<?php

$SF = true;
$table = $REX['TABLE_PREFIX'].'com_user';
$table_field = $REX['TABLE_PREFIX'].'com_user_field';

$bezeichner = "User";

$func = rex_request("func","string","");
$FORM = rex_request("FORM","array","");

//------------------------------
if($func == "add" || $func == "edit")
{
	
	echo $back_to_overview;

	$mita = new rexform;
	// $mita->debug = 1;
	$mita->setWidth(770);
	$mita->setLabelWidth(160);
	$mita->setTablename($table);
	
	$oid = (int) @$_REQUEST["oid"];
	
	if($func == "add"){
		$mita->setFormtype("add");
		$mita->setFormheader("
			<input type=hidden name=page value=".$page." />
			<input type=hidden name=subpage value=".$subpage." />
			<input type=hidden name=func value=".$func." />");
		$mita->setShowFormAlways(false);
		$mita->setValue("subline","$bezeichner erstellen" ,"left",0);
	}else{			
		$mita->setFormtype("edit", "id='".$oid."'", "$bezeichner wurde nicht gefunden");
		$mita->setFormheader("
			<input type=hidden name=page value=".$page.">
			<input type=hidden name=subpage value=".$subpage.">
			<input type=hidden name=func value=".$func." />
			<input type=hidden name=oid value=".$oid.">");
		$mita->setShowFormAlways(false);				
		$mita->setValue("subline","$bezeichner edieren" ,"left",0);
	}

	$mita->setCols(2);
	$mita->setValue("empty","","",0);

	$guf = new rex_sql;
	$guf->setQuery("select * from ".$table_field." where editable=1 order by prior");
	$fields = array();
	$gufa = $guf->getArray();
	foreach($gufa as $key => $value)
	{
	  rex_com_s_rexform($mita,$value);
	}
	
	/*
	if($func == "edit"){
		$mita->setValue("multipleselectsql","Gruppen","",0,
				"select * from rex_com_group order by name","id","name",
				5,"rex_com_group_user","user_id='$oid'","group_id");
	}
	*/
	
	echo $mita->showForm();

	if (!$mita->form_show)
	{
		$func = "";
	}else
	{
		if ($oid > 0 & $func == "edit")
		{

		}
	} 
	
}

//------------------------------> Partner löschen
if($func == "delete"){
	$query = "delete from $table where id='".$oid."' ";
	$delsql = new rex_sql;
	$delsql->debugsql=0;
	$delsql->setQuery($query);
	$func = "";
	
	echo rex_info("User wurde gelöscht");
}



//------------------------------> Userliste
if($func == ""){

	/** Suche  **/
	$addsql = "";
	$link	= "";
	
	$csuchtxt = rex_request("csuchtxt","string","");
	if($csuchtxt != ""){
		$link .= "&csuchtxt=".urlencode($csuchtxt);
	}
	
	$csuchfeld = rex_request("csuchfeld","array");
	$SUCHSEL = new rexselect();
	$SUCHSEL->setMultiple(1); 
	$SUCHSEL->setSize(5); 
	$SUCHSEL->setName("csuchfeld[]");
	$SUCHSEL->setStyle("width:100%;");

	$ssql 	= new rex_sql();
	//$ssql->debugsql = 1;
	$ssql->setQuery("select * from ".$table_field." order by prior");

	for($i=0;$i<$ssql->getRows(); $i++){
		$SUCHSEL->addOption($ssql->getValue("name"),$ssql->getValue("userfield"));
		if(!is_array($csuchfeld))
			$SUCHSEL->setSelected($ssql->getValue("field"));
		$ssql->next();
	}
	foreach($csuchfeld as $cs){
		$SUCHSEL->setSelected($cs);
		$link .= "&csuchfeld[]=".($cs);
	}	
	

	$cstatus = rex_request("cstatus","string");
	$STATUSSEL = new rexselect();
	$STATUSSEL->setName("cstatus");
	$STATUSSEL->setStyle("width:100%;");
	$STATUSSEL->addOption("Aktiv & Inaktiv", "");
	$STATUSSEL->addOption("Aktiv", 1);
	$STATUSSEL->addOption("Inaktiv", 0);	
	if($cstatus != ""){
		$STATUSSEL->setSelected($cstatus);
		$link .= "&cstatus=".urlencode($cstatus);
	}

	$suchform = '<table width=770 cellpadding=5 cellspacing=1 border=0 bgcolor=#ffffff class="rex-table">';
	$suchform .= '<form action="'.$_SERVER['PHP_SELF'].'" method="poost" >';
	$suchform .= '<input type="hidden" name="page" value="'.$page.'" />';
	$suchform .= '<input type="hidden" name="subpage" value="'.$subpage.'" />';
	$suchform .= '<input type="hidden" name="csuche" value="1" />';
	$suchform .= '<tr>
		<th>Suchbegriff</th>
		<th>Tabellenfelder über die gesucht wird</th>
		<th>Status der gesuchten Einträge</th><th>&nbsp;</th>
		</tr>';	
	$suchform .= '<tr>
		<td class="grey" valign="top"><input type="text" name="csuchtxt" value="'.htmlspecialchars(stripslashes($csuchtxt)).'" style="width:100%;" /></td>
		<td class="grey" valign="top">'.$SUCHSEL->out().'</td><td class="grey" valign="top">'.$STATUSSEL->out().'</td>
		<td class="grey" valign="top"><input type="submit" name="send" value="suchen"  class="inp100" /></td>
		</tr>';
	$suchform .= '</form>';
	$suchform .= '</table><br />';
	
	echo $suchform;
	
	if($csuche == 1)
	{
		if(is_array($csuchfeld) && $csuchtxt != ""){
			$addsql .= "WHERE (";
			foreach($csuchfeld as $cs){
				$addsql .= " `".$cs."` LIKE  '%".$csuchtxt."%' OR ";			
			}
			$addsql = substr($addsql, 0, strlen($addsql)-3 );
			$addsql .= ")";
		}	
		$link .= "&csuche]".$csuche;
	}
	if($cstatus != ""){
		if($addsql == ""){ $addsql .= " WHERE "; } else { $addsql .= " AND "; }
		$addsql .= " `status`='".$cstatus."' ";
	}
	
	$sql = "select * from $table $addsql";
	// echo $sql;

	echo "<table cellpadding=5 class=rex-table><tr><td><a href=index.php?page=".$page."&subpage=".$subpage."&func=add><b>+ $bezeichner anlegen</b></a></td></tr></table><br />";
	
	$list = rex_list::factory($sql,30);
	$list->setColumnFormat('id', 'Id');

	/*
	$list->setColumnLabel('name', 'Name');
	$list->setColumnLabel('firma', 'Firma');
	$list->setColumnLabel('funktion', 'Funktion');
	*/

	$list->setColumnParams("id", array("oid"=>"###id###","func"=>"edit"));
	$list->setColumnParams("name", array("oid"=>"###id###","func"=>"edit"));
	$list->setColumnParams("email", array("oid"=>"###id###","func"=>"edit"));

	$list->addParam("page", $page);
	$list->addParam("subpage", $subpage);
	$list->addParam("csuchtxt", $csuchtxt);
	$list->addParam("cstatus", $cstatus );
	$list->addParam("csuche", $csuche );
	foreach($csuchfeld as $cs)
	{
		$list->addParam("csuchfeld[]", $cs);
	}

	$guf = new rex_sql;
	$guf->setQuery("select * from ".$table_field." where inlist<>1 order by prior");
	$gufa = $guf->getArray();
	foreach($gufa as $key => $value)
	{
		$list->removeColumn($value["userfield"]);
	}

	$list->addColumn('löschen','löschen');
	$list->setColumnParams("löschen", array("oid"=>"###id###","func"=>"delete"));
	
	/*
	$list->setColumnSortable('name');
	$list->addColumn('testhead','###id### - ###name###',-1);
	$list->addColumn('testhead2','testbody2');
	$list->setCaption('thomas macht das css');
	*/
	
	echo $list->get();

}


?>