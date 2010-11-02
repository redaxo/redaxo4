<?php

/**
 * Community Install 
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 */

// ----- XFORM Manager definitionen

$fields = array();

$fields[1] = array();
$fields[1]['table_name'] = 'rex_com_user';
$fields[1]['prio'] = 100; 
$fields[1]['type_id'] = 'value'; 
$fields[1]['type_name'] = 'text'; 
$fields[1]['list_hidden'] = 0; 
$fields[1]['f1'] = 'login'; 
$fields[1]['f2'] = 'Login'; 
$fields[1]['search'] = '1';
		
$fields[2] = $fields[1];
$fields[2]['prio'] = 110; 
$fields[2]['type_id'] = 'validate'; 
$fields[2]['type_name'] = 'notEmpty'; 
$fields[2]['list_hidden'] = 1; 
$fields[2]['f1'] = 'login'; 
$fields[2]['f2'] = 'Bitte geben Sie ein Login ein'; 
$fields[2]['search'] = '0';

$fields[3] = $fields[2];
$fields[3]['prio'] = 120; 
$fields[3]['type_name'] = 'unique'; 
$fields[3]['list_hidden'] = 1; 
$fields[3]['f1'] = 'login'; 
$fields[3]['f2'] = 'Dieses Login existiert bereits'; 
$fields[3]['search'] = '0';

$fields[4] = $fields[1];
$fields[4]['prio'] = 200; 
$fields[4]['list_hidden'] = 0; 
$fields[4]['f1'] = 'password'; 
$fields[4]['f2'] = 'Passwort'; 
$fields[4]['search'] = '0';

$fields[5] = $fields[2];
$fields[5]['prio'] = 210; 
$fields[5]['list_hidden'] = 1; 
$fields[5]['f1'] = 'password'; 
$fields[5]['f2'] = 'Bitte gib ein Passwort ein'; 

$fields[6] = $fields[1];
$fields[6]['prio'] = 300; 
$fields[6]['list_hidden'] = 0; 
$fields[6]['f1'] = 'email'; 
$fields[6]['f2'] = 'E-Mail'; 
$fields[6]['search'] = '1';

$fields[7] = $fields[2];
$fields[7]['prio'] = 310; 
$fields[7]['type_name'] = 'email'; 
$fields[7]['list_hidden'] = 1; 
$fields[7]['f1'] = 'email'; 
$fields[7]['f2'] = 'Bitte überprüfe Deine E-Mail'; 
$fields[7]['search'] = '0';

$fields[8] = $fields[1];
$fields[8]['prio'] = 400; 
$fields[8]['type_id'] = 'value'; 
$fields[8]['type_name'] = 'select'; 
$fields[8]['list_hidden'] = 0; 
$fields[8]['f1'] = 'status'; 
$fields[8]['f2'] = 'Status'; 
$fields[8]['f3'] = 'Zugang beantragt=0;Zugang aktiv=1;Zugang inaktiv=-1'; 
$fields[8]['f5'] = '-1'; 
$fields[8]['search'] = '1';

$fields[9] = $fields[1];
$fields[9]['prio'] = 600; 
$fields[9]['list_hidden'] = 0; 
$fields[9]['f1'] = 'firstname'; 
$fields[9]['f2'] = 'Vorname'; 
$fields[9]['search'] = '1';


$fields[10] = $fields[1];
$fields[10]['prio'] = 700; 
$fields[10]['list_hidden'] = 0; 
$fields[10]['f1'] = 'name'; 
$fields[10]['f2'] = 'Nachname'; 
$fields[10]['search'] = '1';

$fields[11] = $fields[1];
$fields[11]['prio'] = 500; 
$fields[11]['list_hidden'] = 0; 
$fields[11]['f1'] = 'activation_key'; 
$fields[11]['f2'] = 'Aktivierungsschlüssel'; 
$fields[11]['search'] = '1';

if (OOAddon::isAvailable('xform') != 1 || OOAddon::isAvailable('phpmailer') != 1)
{
	// Installation nicht erfolgreich
	$REX['ADDON']['install']['community'] = 0;
	$REX['ADDON']['installmsg']['community'] = 'AddOn "XForm" und/oder "PHPMailer" ist nicht installiert und aktiviert.';

}elseif(OOAddon::getVersion('xform') < "2.2")
{
  $REX['ADDON']['install']['community'] = 0;
  $REX['ADDON']['installmsg']['community'] = 'Das AddOn "XForm" muss mindestens in der Version 2.2 vorhanden sein.';

}elseif(!rex_xform_manager::createBasicSet('com'))
{

  $REX['ADDON']['install']['community'] = 0;
  $REX['ADDON']['installmsg']['community'] = 'Der XForm Manager hat das BasicSet nicht installieren können.';

}elseif(!rex_xform_manager::createTable('com',"rex_com_user",array('search'=>1)))
{

  $REX['ADDON']['install']['community'] = 0;
  $REX['ADDON']['installmsg']['community'] = 'Der XForm Manager konnte die Tabelle und Zuweisungen zu "rex_com_user" nicht anlegen.';

}elseif(!rex_xform_manager::addDataFields('com','rex_com_user',$fields))
{

  $REX['ADDON']['install']['community'] = 0;
  $REX['ADDON']['installmsg']['community'] = 'Der XForm Manager hat die Data-Tabellen-Felder nicht anlegen können.';

}else
{

	$REX['ADDON']['install']['community'] = 1;
	// XForm vorhanden -> install.sql wird automatisch ausgeführt.

}

?>