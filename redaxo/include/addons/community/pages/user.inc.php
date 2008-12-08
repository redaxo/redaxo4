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
	$ssql 	= new rex_sql();
	// $ssql->debugsql = 1;
	$ssql->setQuery("DESCRIBE ".$table.";");
	
	$csuchtxt = rex_request("FORM[csuchtxt]","string","");
	
	if($csuchtxt != ""){
		$link .= "&FORM[csuchtxt]=".urlencode($csuchtxt);
	}
	
	$SUCHSEL = new rexselect();
	$SUCHSEL->setMultiple(1); 
	$SUCHSEL->setSize(5); 
	$SUCHSEL->setName("FORM[csuchfeld][]");
	$SUCHSEL->setStyle("width:100%;");
	for($i=0;$i<$ssql->getRows(); $i++){
		$SUCHSEL->addOption($ssql->getValue("field"),$ssql->getValue("field"));
		if(!is_array($FORM['csuchfeld']))
			$SUCHSEL->setSelected($ssql->getValue("field"));
		$ssql->next();
	}
	if(isset($FORM['csuchfeld']) && is_array($FORM['csuchfeld'])){
		foreach($FORM['csuchfeld'] as $cs){
			$SUCHSEL->setSelected($cs);
			$link .= "&FORM[csuchfeld][]=".($cs);
		}	
	}
	
	$STATUSSEL = new rexselect();
	$STATUSSEL->setName("FORM[cstatus]");
	$STATUSSEL->setStyle("width:100%;");
	if(isset($FORM['cstatus']) && $FORM['cstatus'] != ""){
		$STATUSSEL->setSelected($FORM['cstatus']);
		$link .= "&FORM[cstatus]=".urlencode($FORM['cstatus']);
	}
	$STATUSSEL->addOption("Aktiv & Inaktiv", "");
	$STATUSSEL->addOption("Aktiv", 1);
	$STATUSSEL->addOption("Inaktiv", 0);	
	
	
	$suchform = '<table width=770 cellpadding=5 cellspacing=1 border=0 bgcolor=#ffffff class="rex-table">';
	$suchform .= '<form action="'.$_SERVER['PHP_SELF'].'" method="poost" >';
	$suchform .= '<input type="hidden" name="page" value="'.$page.'" />';
	$suchform .= '<input type="hidden" name="subpage" value="'.$subpage.'" />';
	$suchform .= '<input type="hidden" name="FORM[csuche]" value="1" />';
	$suchform .= '<tr><th>Suchbegriff</th><th>Tabellenfelder über die gesucht wird</th><th>Status der gesuchten Einträge</th><th>&nbsp;</th></tr>';	
	$suchform .= '<tr><td class="grey" valign="top"><input type="text" name="FORM[csuchtxt]" value="'.htmlspecialchars(stripslashes($csuchtxt)).'" style="width:100%;" /></td><td class="grey" valign="top">'.$SUCHSEL->out().'</td><td class="grey" valign="top">'.$STATUSSEL->out().'</td><td class="grey" valign="top"><input type="submit" name="FORM[send]" value="suchen"  class="inp100" /></td></tr>';
	$suchform .= '</form>';
	$suchform .= '</table><br />';
	
	// echo $suchform;
	
	if(isset($FORM['csuche']) && $FORM['csuche'] == 1){
		
		if(is_array($FORM['csuchfeld']) && $csuchtxt != ""){
			$addsql .= "WHERE (";
			foreach($FORM['csuchfeld'] as $cs){
				$addsql .= " `".$cs."` LIKE  '%".$csuchtxt."%' OR ";			
			}
			$addsql = substr($addsql, 0, strlen($addsql)-3 );
			$addsql .= ")";
		}	
		$link .= "&FORM[csuche]=".$FORM['csuche'];
	}
	if(isset($FORM['cstatus']) && $FORM['cstatus'] != ""){
		if($addsql == ""){ $addsql .= " WHERE "; } else { $addsql .= " AND "; }
		$addsql .= " `status`='".$FORM['cstatus']."' ";
	}
	
	
	$sql = "select * from $table $addsql";

	//echo $sql;

	echo "<table cellpadding=5 class=rex-table><tr><td><a href=index.php?page=".$page."&subpage=".$subpage."&func=add><b>+ $bezeichner anlegen</b></a></td></tr></table><br />";
	
	
	$mit = new rexlist;
	$mit->setQuery($sql);
	$mit->setList(100);
	$mit->setGlobalLink("index.php?page=".$page."&subpage=".$subpage."".$link."&next=");
	$mit->setValue("id","id");
	$mit->setLink("index.php?page=".$page."&subpage=".$subpage."&func=edit&oid=","id");
	$mit->setValueOrder(1);


	$guf = new rex_sql;
	$guf->setQuery("select * from ".$table_field." where inlist=1 order by prior");
	$fields = array();
	$gufa = $guf->getArray();
	foreach($gufa as $key => $value)
	{
		rex_com_s_rexlist($mit,$value);
	}

	$mit->setValue("löschen","");
	$mit->setFormat("ifempty", "- löschen");
	$mit->setFormat("link","index.php?page=".$page."&subpage=".$subpage."&func=delete&oid=","id",""," onclick=\"return confirm('sicher löschen ?');\"");	
	if (isset($FORM["ordername"]) && isset($FORM["ordertype"])) $mit->setOrder($FORM["ordername"],$FORM["ordertype"]);
	$next = rex_request("next","int","0");
	echo $mit->showall($next);

	

}


?>