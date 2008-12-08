<?php

if ($REX["REDAXO"])
{

	// Diese Seite noch extra einbinden
	$REX['ADDON']['community']['subpages'][] = array('plugin.setup','Setup');
	
	// Module für das Setup aufnehmen
	$REX["ADDON"]["community"]["plugins"]["setup"]["modules"][] = array("setup","tabbox","1001 - COM-Module - Tabbox");
	$REX["ADDON"]["community"]["plugins"]["setup"]["modules"][] = array("setup","usersearch","1002 - COM-Module - Usersuche");
	$REX["ADDON"]["community"]["plugins"]["setup"]["modules"][] = array("setup","userdetails","1003 - COM-Module - Userdetails");

	// Templates für das Setup aufnehmen
	$REX["ADDON"]["community"]["plugins"]["setup"]["templates"][] = array("setup","main","1011 - COM-Template - Standardtemplate",1);

	$REX["ADDON"]["community"]["plugins"]["setup"]["templates"][] = array("setup","auth","1012 - COM-Template - Authentifizierung",0);
	$REX["ADDON"]["community"]["plugins"]["setup"]["templates"][] = array("setup","permission","1013 - COM-Template - Permission/Rechte",0);
	$REX["ADDON"]["community"]["plugins"]["setup"]["templates"][] = array("setup","userlogin","1014 - COM-Template - Userloginfenster",0);

	$REX["ADDON"]["community"]["plugins"]["setup"]["templates"][] = array("setup","navi","1015 - COM-Template - Navigation mit 3 Ebenen",0);
	$REX["ADDON"]["community"]["plugins"]["setup"]["templates"][] = array("setup","navi_user","1016 - COM-Template - Navigation - Userbereiche 1 Ebene",0);

	$REX["ADDON"]["community"]["plugins"]["setup"]["templates"][] = array("setup","breadcrump","1017 - COM-Template - Breadcrumb",0);


	// E-Mail Templates für das Setup aufnehmen
	$REX["ADDON"]["community"]["plugins"]["setup"]["emails"][] = array("setup","register","register","Community: Bitte bestätigen Sie die Registrierung", $REX['ERROR_EMAIL'], $REX['ERROR_EMAIL']);
	$REX["ADDON"]["community"]["plugins"]["setup"]["emails"][] = array("setup","send_password","send_password","Community: Neues Passwort", $REX['ERROR_EMAIL'], $REX['ERROR_EMAIL']);

	// Ids
	$REX["ADDON"]["community"]["plugins"]["setup"]["ids"][] = "REX_COM_PAGE_PROFIL_ID";
	$REX["ADDON"]["community"]["plugins"]["setup"]["ids"][] = "REX_COM_PAGE_MYPROFIL_ID";
	$REX["ADDON"]["community"]["plugins"]["setup"]["ids"][] = "REX_COM_PAGE_REGISTER_ID";
	$REX["ADDON"]["community"]["plugins"]["setup"]["ids"][] = "REX_COM_PAGE_REGISTER_ACCEPT_ID";
	$REX["ADDON"]["community"]["plugins"]["setup"]["ids"][] = "REX_COM_PAGE_PSWFORGOTTEN_ID";
	$REX["ADDON"]["community"]["plugins"]["setup"]["ids"][] = "REX_COM_PAGE_LOGIN_ID";
	$REX["ADDON"]["community"]["plugins"]["setup"]["ids"][] = "REX_COM_USERCAT_ID";

}

// Allgemeine Config

// ----------------- DONT EDIT BELOW THIS
// --- DYN
define('REX_COM_PAGE_PROFIL_ID',10);
define('REX_COM_PAGE_MYPROFIL_ID',3);
define('REX_COM_PAGE_REGISTER_ID',24);
define('REX_COM_PAGE_REGISTER_ACCEPT_ID',26);
define('REX_COM_PAGE_PSWFORGOTTEN_ID',25);
define('REX_COM_PAGE_LOGIN_ID',1);
define('REX_COM_USERCAT_ID',2);
define('REX_COM_PAGE_SENDMESSAGE_ID',6);
// --- /DYN
// ----------------- /DONT EDIT BELOW THIS


?>