<?php

// MODUL OUT - XO FORM
// v0.19
//--------------------------------------------------------------------------------

$xform = new rex_xform;
if ("REX_VALUE[7]" == 1) 
	$xform->setDebug(TRUE);

$form_data = 'REX_VALUE[3]';
$form_data = trim(str_replace("<br />","",rex_xform::unhtmlentities($form_data)));
$xform->setFormData($form_data);
$xform->setRedaxoVars(REX_ARTICLE_ID,REX_CLANG_ID); 

// Action/en

// Datein vorher auslesen ?
if ("REX_VALUE[10]" != "") 
	$xform->setObjectparams("getdata",TRUE); 

$xform->setObjectparams("main_table","REX_VALUE[8]"); // fuer db speicherungen und unique abfragen
$xform->setObjectparams("form_type","REX_VALUE[1]"); // Aktion einstellen - Nichts, Speichern, E-Mail, beides ? - form_typ

// E-Mail Action
$xform->setObjectparams("mail_subject","REX_VALUE[4]"); // Mailsubject
$xform->setObjectparams("mail_body",str_replace("<br />","",rex_xform::unhtmlentities('REX_VALUE[5]'))); // Mailsubject
$xform->setObjectparams("mail_to",$REX['ERROR_EMAIL']); // Mail to
if ("REX_VALUE[2]" != "") 
	$xform->setObjectparams("mail_from","REX_VALUE[2]"); // Mail from
else 
	$xform->setObjectparams("mail_from",$REX['ERROR_EMAIL']); // Mail from

// Individuelle Einstellungen
?>REX_PHP_VALUE[9]<?php

if("REX_IS_VALUE[6]" == "true")
{
  $html = "0";
  if('REX_VALUE[11]' == 1) $html = "1";

  $xform->setActionField("showtext",array(
				'REX_HTML_VALUE[6]',
				'<div class="rex-message"><div class="rex-info"><p>',
				'</p></div></div>',
				$html // als HTML interpretieren
			)
		);
}

// Ausgabe
echo $xform->getForm();

?>