<?php

/**
 * auth
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */


$error = '';


// ******************************************************* Feld bei User anlegen

$fields_user = array();
$fields_user[1] = array();
$fields_user[1]['table_name'] = 'rex_com_user';
$fields_user[1]['prio'] = 800; 
$fields_user[1]['type_id'] = 'value'; 
$fields_user[1]['type_name'] = 'text'; 
$fields_user[1]['f1'] = 'session_key'; 
$fields_user[1]['f2'] = 'Sessionkey - angemeldet bleiben';
$fields_user[1]['list_hidden'] = 1;

// Version 4.3.1 .. REX Array wird bei PlugIns überschrieben.. deswegen
$REXADDON = $REX['ADDON'];
$REX['ADDON'] = $ADDONSsic; // Kommt aus class.rex_manager.inc.php unter plugin_manager::addon2plugin
if(!rex_xform_manager::addDataFields('com','rex_com_user',$fields_user)){ $error = 'Der XForm Manager hat das User-Tabellen-Feld nicht anlegen können.'; }
$REX['ADDON'] = $REXADDON;



// ******************************************************************** METAINFO

// Art der Gruppenrechte
$a = new rex_sql;
$a->setTable("rex_62_params");
$a->setValue("title","Art der Gruppenrechte");
$a->setValue("name","art_com_grouptype");
$a->setValue("prior","10");
$a->setValue("type","3");
$a->setValue("params","0:Für alle, egal welche Gruppe|1:Muss in jeder Gruppe sein|2:Muss in einer Gruppe sein|3:Hat keine Gruppen");
$a->setValue("validate",NULL);
$a->addGlobalCreateFields();
$g = new rex_sql;
$g->setQuery('select * from rex_62_params where name="art_com_grouptype"');
if ($g->getRows()==1)
{
	$a->setWhere('name="art_com_grouptype"');
	$a->update();

}else 
{
	$a->insert();
}
$g = new rex_sql;
$g->setQuery('show columns from rex_article Like "art_com_grouptype"');
if ($g->getRows()==0) 
{
	$a->setQuery("ALTER TABLE `rex_article` ADD `art_com_grouptype` VARCHAR( 255 ) NOT NULL"); 
}

// Zugriffsrechte
$a = new rex_sql;
$a->setTable("rex_62_params");
$a->setValue("title","Zugriffsrechte");
$a->setValue("name","art_com_permtype");
$a->setValue("prior","11");
$a->setValue("type","3");
$a->setValue("params","0:Für Alle|1:Nur für eingeloggte User|2:Nur für nicht eingeloggte User");
$a->setValue("validate",NULL);
$a->addGlobalCreateFields();
$g = new rex_sql;
$g->setQuery('select * from rex_62_params where name="art_com_permtype"');
if ($g->getRows()==1)
{
	$a->setWhere('name="art_com_permtype"');
	$a->update();

}else 
{
	$a->insert();
}
$g = new rex_sql;
$g->setQuery('show columns from rex_article Like "art_com_permtype"');
if ($g->getRows()==0) 
{
	$a->setQuery("ALTER TABLE `rex_article` ADD `art_com_permtype` VARCHAR( 255 ) NOT NULL"); 
}

// Gruppen
$a = new rex_sql;
$a->setTable("rex_62_params");
$a->setValue("title","Gruppen");
$a->setValue("name","art_com_groups");
$a->setValue("prior","12");
$a->setValue("type","3");
$a->setValue("attributes","multiple=multiple");
$a->setValue("params","select name as label,id from rex_com_group order by label");
$a->setValue("validate",NULL);
$a->addGlobalCreateFields();
$g = new rex_sql;
$g->setQuery('select * from rex_62_params where name="art_com_groups"');
if ($g->getRows()==1)
{
	$a->setWhere('name="art_com_groups"');
	$a->update();
}else 
{
	$a->insert();
}
$g = new rex_sql;
$g->setQuery('show columns from rex_article Like "art_com_groups"');
if ($g->getRows()==0) 
{
	$a->setQuery("ALTER TABLE `rex_article` ADD `art_com_groups` VARCHAR( 255 ) NOT NULL"); 
}

// ************************************************************** CACHE LOESCHEN

$info = rex_generateAll(); // quasi kill cache .. 

$REX['ADDON']['install']['auth'] = 1;
if($error != "")
{
	$REX['ADDON']['install']['auth'] = 0;
	$REX['ADDON']['installmsg']['auth'] = $error;
}


?>