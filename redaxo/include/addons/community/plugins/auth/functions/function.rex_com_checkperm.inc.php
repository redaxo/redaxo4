<?php

/*

	art_com_permtype	Zugriffsrechte	0:Für Alle|1:Nur für eingeloggte User|2:Nur für nicht eingeloggte User		0
		o Für Alle - 0
		o Nur für eingeloggte User - 1
		o Nur für nicht eingeloggte User - 2

	art_com_grouptype	Art der Gruppenrechte	0:Für alle, egal welche Gruppe|1:Muss in jeder Gruppe sein|2:Muss in einer Gruppe sein|3:Hat keine Gruppen		0
		o Für alle, egal welche Gruppe - 0
		o Muss in jeder Gruppe sein - 1
		o Muss in einer Gruppe sein - 2
		o Hat keine Gruppen - 3

	art_com_groups		Gruppen		select name as label,id from rex_com_group order by label		multiple=multiple

*/

function rex_com_checkperm(&$obj)
{
	
	global $REX;

	// Authentifizierung ist ausgeschaltet
	if($REX['ADDON']['community']['plugin_auth']['auth_active'] != "1")
		return TRUE;

	// echo "<br />*".$obj->getValue('art_com_permtype');
	// echo " -- ".$obj->getValue('art_com_groups');
	// if(isset($REX["COM_USER"])) echo " ## ".$REX["COM_USER"]->getValue("group");
	
	// ---- Wenn für alle freigegeben
	if($obj->getValue('art_com_permtype') == 0 || $obj->getValue('art_com_permtype') == "")
		return TRUE;

	// ---- nur für nicht eingeloggte freigegeben
	if($obj->getValue('art_com_permtype') == 2)
		if(!isset($REX["COM_USER"]) || !is_object($REX["COM_USER"]))
			return TRUE;
		else
			return FALSE;

	if($obj->getValue('art_com_permtype') == 1 && (!isset($REX["COM_USER"]) || !is_object($REX["COM_USER"])))
		return FALSE;

	// ---------- ab hier nur für eingeloggte -> permtype = 1

	// ----- wenn für alle gruppen freigegeben
	if($obj->getValue('art_com_grouptype') == 0 || $obj->getValue('art_com_grouptype') == "")
		return TRUE;

	// ----- muss in jeder gruppe sein
	if($obj->getValue('art_com_grouptype') == 1)
	{
		$art_groups = explode("|",$obj->getValue('art_com_groups'));
		$user_groups = explode(",",$REX["COM_USER"]->getValue("group"));
		foreach($art_groups as $ag)
		{
			if($ag != "" && !in_array($ag,$user_groups))
			{
				return FALSE;
			}
		}
	}
	
	// ----- muss nur in einer gruppe sein
	if($obj->getValue('art_com_grouptype') == 2)
	{
		$art_groups = explode("|",$obj->getValue('art_com_groups'));
		$user_groups = explode(",",$REX["COM_USER"]->getValue("group"));
		foreach($art_groups as $ag)
		{
			if($ag != "" && in_array($ag,$user_groups))
			{
				return TRUE;
			}
		}
	}
	
	// ----- ist in keiner gruppe
	if($obj->getValue('art_com_grouptype') == 3)
	{
		$user_groups = explode(",",$REX["COM_USER"]->getValue("group"));
		if(count($user_groups) == 0)
			return TRUE;
	}
	
	return FALSE;

}

?>