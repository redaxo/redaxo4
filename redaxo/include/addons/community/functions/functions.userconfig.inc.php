<?php

// allgemeine feldtypen
$UT = array();
$UT[1] = "INT(11)";
$UT[2] = "VARCHAR(255)";
$UT[3] = "TEXT";
$UT[4] = "PASSWORD";
$UT[5] = "SELECT";
$UT[6] = "BOOL";

// feste felder
$ff = array();
$ff[] = "id";

$ff[] = "login";
$ff[] = "password";
$ff[] = "email";
$ff[] = "status";

$ff[] = "session_id";
$ff[] = "last_xs";
$ff[] = "last_login";
$ff[] = "email_checked";
$ff[] = "activation_key";
$ff[] = "last_newsletterid";

$ff[] = "gender";
$ff[] = "name";
$ff[] = "firstname";
$ff[] = "street";
$ff[] = "zip";
$ff[] = "city";
$ff[] = "phone";
$ff[] = "birthday";

$ff[] = "show_contactinfo";
$ff[] = "show_personalinfo";
$ff[] = "show_guestbook";
$ff[] = "sendemail_contactrequest";
$ff[] = "sendemail_newmessage";
$ff[] = "sendemail_guestbook";

$REX["ADDON"]["community"]["ut"] = $UT;
$REX["ADDON"]["community"]["ff"] = $ff;

function rex_com_utcreate($table,$feld,$type,$extra1="",$extra2="",$extra3="")
{
	$err_msg = "<b>$feld</b> fehlte in der Usertabelle und wurde nun angelegt.";
	
	$up = new rex_sql;
	switch($type)
	{
		case("1"):
			// int anlegen
			$up->setQuery("ALTER TABLE `$table` ADD `$feld` INT(11) NOT NULL;");
			break;
		case("2"):
			// varchar anlegen
			$up->setQuery("ALTER TABLE `$table` ADD `$feld` VARCHAR(255) NOT NULL;");
			break;
		case("3"):
			// text anlegen
			$up->setQuery("ALTER TABLE `$table` ADD `$feld` TEXT NOT NULL;");
			break;
		case("4"):
			// varchar anlegen
			$up->setQuery("ALTER TABLE `$table` ADD `$feld` VARCHAR(255) NOT NULL;");
			break;
		case("5"):
			// varchar anlegen
			$up->setQuery("ALTER TABLE `$table` ADD `$feld` VARCHAR(255) NOT NULL;");
			break;
		case("6"):
			// tinyint(4) anlegen
			$up->setQuery("ALTER TABLE `$table` ADD `$feld` TINYINT NOT NULL;");
			break;
		default:
			// fehler - typ nicht vorhanden
			$err_msg = "Typ <b>$type</b> nicht gefunden. Feld <b>$feld</b> konnte nicht angelegt werden.";
			break;
	}
	return $err_msg;
}

function rex_com_s_rexlist(&$list,$value)
{
	$list->setValue($value["name"],$value["userfield"]);

	switch($value["type"])
	{
		case("5"):
			// select
			$extra1 = str_replace("=","|",$value["extra1"]);
			$list->setFormat("replace_value",$value["extra1"]);
			break;
		case("6"):
			// bool
			$list->setFormat("replace_value","0|nein|1|ja");
			break;
	}

	switch($value["name"])
	{
		case("status"):
			$list->setFormat("replace_value",'|<span style="color:#c33;">inaktiv</span>|1|<span style="color:#3c3;">aktiv</span>');
			break;
	}
	
	$list->setValueOrder(1);
	
}

function rex_com_s_rexform(&$form,$value)
{
	switch($value["type"])
	{
		case("5"):
			// select
			$extra1 = str_replace("=","|",$value["extra1"]);
			$form->setValue("singleselect",$value["name"],$value["userfield"],$value["mandatory"],$extra1);
			break;
		case("6"):
			// bool
			$form->setValue("checkbox",$value["name"],$value["userfield"]);
			break;
		default:
			$value["mandatory"] = (int) $value["mandatory"];
			$form->setValue("text",$value["name"],$value["userfield"],$value["mandatory"]);
			break;
	}
}

?>