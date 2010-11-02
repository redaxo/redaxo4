<?php

/**
 * XO-Form
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

$fields_user = array();
	$fields_user[1] = array();
	$fields_user[1]['table_name'] = 'rex_com_user';
	$fields_user[1]['prio'] = 300; 
	$fields_user[1]['type_id'] = 'value'; 
	$fields_user[1]['type_name'] = 'be_manager_relation'; 
	$fields_user[1]['f1'] = 'group'; 
	$fields_user[1]['f2'] = 'Gruppen';
	$fields_user[1]['f3'] = 'rex_com_group';
	$fields_user[1]['f4'] = 'name'; 
	$fields_user[1]['f5'] = 1; 
	$fields_user[1]['f6'] = 1; 
	$fields_user[1]['list_hidden'] = 0; 

$fields_group = array();
	$fields_group[1] = array();
	$fields_group[1]['table_name'] = 'rex_com_group';
	$fields_group[1]['prio'] = 110; 
	$fields_group[1]['type_id'] = 'validate'; 
	$fields_group[1]['type_name'] = 'notEmpty'; 
	$fields_group[1]['f1'] = 'name'; 
	$fields_group[1]['f2'] = 'Bitte geben Sie den Gruppennamen ein';
	$fields_group[1]['list_hidden'] = 1; 
	$fields_group[2] = array();
	$fields_group[2]['table_name'] = 'rex_com_group';
	$fields_group[2]['prio'] = 100; 
	$fields_group[2]['type_id'] = 'value'; 
	$fields_group[2]['type_name'] = 'text'; 
	$fields_group[2]['list_hidden'] = 0; 
	$fields_group[2]['f1'] = 'name'; 
	$fields_group[2]['f2'] = 'Gruppenname';

// Version 4.3.1 .. REX Array wird bei PlugIns überschrieben.. deswegen
$REXADDON = $REX['ADDON'];
$REX['ADDON'] = $ADDONSsic; // Kommt aus class.rex_manager.inc.php unter plugin_manager::addon2plugin

$installed = 0;
$message = '';

if(!rex_xform_manager::createTable('com',"rex_com_group",array()))
{
	$message = 'Der XForm Manager konnte die Tabelle und Zuweisungen zu "rex_com_group" nicht anlegen.';
	
}elseif(!rex_xform_manager::addDataFields('com','rex_com_user',$fields_user))
{
	$message = 'Der XForm Manager hat die User-Tabellen-Felder nicht anlegen können.';
}elseif(!rex_xform_manager::addDataFields('com','rex_com_group',$fields_group))
{
	$message = 'Der XForm Manager hat die Gruppen-Tabellen-Felder nicht anlegen können.';
}else
{
	$installed = 1;
}

$REX['ADDON'] = $REXADDON;
$REX['ADDON']['install']['group'] = $installed;
$REX['ADDON']['installmsg']['group'] = $message;
















?>