<?

// setup.php
// 
// erstellt 01.01.2004
// pergopa kristinus gbr
// lange strasse 31
// 60311 Frankfurt/M.
// www.pergopa.de
// ersteller: j.kristinus

// update
// 04.05.2004 - register_globals wird überprüft


$REX[HTDOCS_PATH] = "../";
include "include/master.inc.php";

$MSG[err] = "";
$MSG[good] = "";

include $REX[INCLUDE_PATH]."/layout_redaxo/top.php";

echo "<script language=Javascript>
<!--
var redaxo = true;
//-->
</script>
<style type=text/css>
	.ok	  { color:#3EC94A; }
	.nice	  { color:#33aa33; }
	.error	  { color:#cc3333; }
</style>";


function setuptitle($title)
{
	title("$title","");

	echo "
	<table border=0 cellpadding=5 cellspacing=1 width=770>
	<form action=setup.php method=post>
	<tr><td class=lgrey><font class=content>";
	
}



// ---------------------------------- MODUS 0 | Start

if (!($checkmodus>0 && $checkmodus<10))
{
	
	
	setuptitle("SETUP: START");
	
	echo "<b>Willkommen bei der Installationsroutine von REDAXO.</b><br><br>Sie werden nun durch verschiedene 
	Testszenarien geleitet die Ihnen helfen sollen Einstellungen bei REDAXO vorzunehmen. Dabei werden Rechte 
	überprüft und verschiedene Angaben abgefragt. Sobald Sie alle Schritte durchlaufen haben können Sie
	REDAXO unter /redaxo/index.php aufrufen und benutzen. <br><br> Sollten Sie noch Fragen haben, so können 
	Sie unter <a href=http://www.redaxo.de/ target=_blank>http://www.redaxo.de/</a> Ihre Kommentare und Fragen loswerden.
	<br><br>Diese Setup-Routine läuft nicht einwandfrei unter Windowsservern. Bitte aktualisieren Sie in diesem Fall die 
	master.inc.php manuell.<br><br>
	
	<div id=lizenz style='width:100%; height:300px; overflow:auto; background-color:#ffffff; text-align:left; font-size:9px;'>

<b>LIZENZBESTIMMUNGEN</b><br><br>Bevor Sie REDAXO für Ihre eigene Zwecke nutzen möchten, berücksichtigen Sie bitte 
die folgenden lizenzrechtlichen Bestimmungen. Es handelt sich hierbei um eine 
Open Source Lizenzvereinbarung, die als General Public License (GPL) in 
englischer Sprache vorliegt.

<br><br>Folgenden Informationen müssen beim Einsatz von REDAXO im Impressum oder auf 
der Kontaktseite aufgenommen werden.

<br><br>CMS: REDAXO - Open Source Content Management System
<br>LINK: www.redaxo.de

<br><br>TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION

<br><br>0. This License applies to any program or other work which contains a notice 
placed by the copyright holder saying it may be distributed under the terms of 
this General Public License. The \"Program\", below, refers to any such program or 
work, and a \"work based on the Program\" means either the Program or any 
derivative work under copyright law: that is to say, a work containing the 
Program or a portion of it, either verbatim or with modifications and/or 
translated into another language. (Hereinafter, translation is included without 
limitation in the term \"modification\".) Each licensee is addressed as \"you\".

<br><br>Activities other than copying, distribution and modification are not covered by 
this License; they are outside its scope. The act of running the Program is not 
restricted, and the output from the Program is covered only if its contents 
constitute a work based on the Program (independent of having been made by 
running the Program). Whether that is true depends on what the Program does.

<br><br>1. You may copy and distribute verbatim copies of the Program's source code as 
you receive it, in any medium, provided that you conspicuously and appropriately 
publish on each copy an appropriate copyright notice and disclaimer of warranty; 
keep intact all the notices that refer to this License and to the absence of any 
warranty; and give any other recipients of the Program a copy of this License 
along with the Program.

<br><br>You may charge a fee for the physical act of transferring a copy, and you may 
at your option offer warranty protection in exchange for a fee.

<br><br>2. You may modify your copy or copies of the Program or any portion of it, thus 
forming a work based on the Program, and copy and distribute such modifications 
or work under the terms of Section 1 above, provided that you also meet all of 
these conditions:

<br><br>a) You must cause the modified files to carry prominent notices stating that you 
changed the files and the date of any change.

<br><br>b) You must cause any work that you distribute or publish, that in whole or in 
part contains or is derived from the Program or any part thereof, to be licensed 
as a whole at no charge to all third parties under the terms of this License.

<br><br>c) If the modified program normally reads commands interactively when run, you 
must cause it, when started running for such interactive use in the most ordinary 
way, to print or display an announcement including an appropriate copyright 
notice and a notice that there is no warranty (or else, saying that you provide a 
warranty) and that users may redistribute the program under these conditions, and 
telling the user how to view a copy of this License. (Exception: if the Program 
itself is interactive but does not normally print such an announcement, your work 
based on the Program is not required to print an announcement.)

<br><br>These requirements apply to the modified work as a whole. If identifiable 
sections of that work are not derived from the Program, and can be reasonably 
considered independent and separate works in themselves, then this License, and 
its terms, do not apply to those sections when you distribute them as separate 
works. But when you distribute the same sections as part of a whole which is a 
work based on the Program, the distribution of the whole must be on the terms of 
this License, whose permissions for other licensees extend to the entire whole, 
and thus to each and every part regardless of who wrote it.

<br><br>Thus, it is not the intent of this section to claim rights or contest your rights 
to work written entirely by you; rather, the intent is to exercise the right to 
control the distribution of derivative or collective works based on the Program.

<br><br>In addition, mere aggregation of another work not based on the Program with the 
Program (or with a work based on the Program) on a volume of a storage or 
distribution medium does not bring the other work under the scope of this License.

<br><br>3. You may copy and distribute the Program (or a work based on it, under Section 
2) in object code or executable form under the terms of Sections 1 and 2 above 
provided that you also do one of the following:

<br><br>a) Accompany it with the complete corresponding machine-readable source code, 
which must be distributed under the terms of Sections 1 and 2 above on a medium 
customarily used for software interchange; or,

<br><br>b) Accompany it with a written offer, valid for at least three years, to give 
any third party, for a charge no more than your cost of physically performing 
source distribution, a complete machine-readable copy of the corresponding 
source code, to be distributed under the terms of Sections 1 and 2 above on a 
medium customarily used for software interchange; or,

<br><br>c) Accompany it with the information you received as to the offer to distribute 
corresponding source code. (This alternative is allowed only for noncommercial 
distribution and only if you received the program in object code or executable 
form with such an offer, in accord with Subsection b above.)

<br><br>The source code for a work means the preferred form of the work for making 
modifications to it. For an executable work, complete source code means all the 
source code for all modules it contains, plus any associated interface definition 
files, plus the scripts used to control compilation and installation of the 
executable. However, as a special exception, the source code distributed need not 
include anything that is normally distributed (in either source or binary form) 
with the major components (compiler, kernel, and so on) of the operating system 
on which the executable runs, unless that component itself accompanies the 
executable.

<br><br>If distribution of executable or object code is made by offering access to copy 
from a designated place, then offering equivalent access to copy the source code 
from the same place counts as distribution of the source code, even though third 
parties are not compelled to copy the source along with the object code.

<br><br>4. You may not copy, modify, sublicense, or distribute the Program except as 
expressly provided under this License. Any attempt otherwise to copy, modify, 
sublicense or distribute the Program is void, and will automatically terminate 
your rights under this License. However, parties who have received copies, or 
rights, from you under this License will not have their licenses terminated so 
long as such parties remain in full compliance.

<br><br>5. You are not required to accept this License, since you have not signed it. 
However, nothing else grants you permission to modify or distribute the Program 
or its derivative works. These actions are prohibited by law if you do not accept 
this License. Therefore, by modifying or distributing the Program (or any work 
based on the Program), you indicate your acceptance of this License to do so, and 
all its terms and conditions for copying, distributing or modifying the Program 
or works based on it.

<br><br>6. Each time you redistribute the Program (or any work based on the Program), 
the recipient automatically receives a license from the original licensor to 
copy, distribute or modify the Program subject to these terms and conditions. 
You may not impose any further restrictions on the recipients' exercise of the 
rights granted herein. You are not responsible for enforcing compliance by 
third parties to this License.

<br><br>7. If, as a consequence of a court judgment or allegation of patent infringement 
or for any other reason (not limited to patent issues), conditions are imposed on 
you (whether by court order, agreement or otherwise) that contradict the conditions 
of this License, they do not excuse you from the conditions of this License. If 
you cannot distribute so as to satisfy simultaneously your obligations under this 
License and any other pertinent obligations, then as a consequence you may not 
distribute the Program at all. For example, if a patent license would not permit 
royalty-free redistribution of the Program by all those who receive copies 
directly or indirectly through you, then the only way you could satisfy both it 
and this License would be to refrain entirely from distribution of the Program.

<br><br>If any portion of this section is held invalid or unenforceable under any 
particular circumstance, the balance of the section is intended to apply and the 
section as a whole is intended to apply in other circumstances.

<br><br>It is not the purpose of this section to induce you to infringe any patents or 
other property right claims or to contest validity of any such claims; this section 
has the sole purpose of protecting the integrity of the free software distribution 
system, which is implemented by public license practices. Many people have made 
generous contributions to the wide range of software distributed through that 
system in reliance on consistent application of that system; it is up to the 
author/donor to decide if he or she is willing to distribute software through any 
other system and a licensee cannot impose that choice.

<br><br>This section is intended to make thoroughly clear what is believed to be a 
consequence of the rest of this License.

<br><br>8. If the distribution and/or use of the Program is restricted in certain countries 
either by patents or by copyrighted interfaces, the original copyright holder who 
places the Program under this License may add an explicit geographical distribution 
limitation excluding those countries, so that distribution is permitted only in or 
among countries not thus excluded. In such case, this License incorporates the 
limitation as if written in the body of this License.

<br><br>9. The Free Software Foundation may publish revised and/or new versions of the 
General Public License from time to time. Such new versions will be similar in 
spirit to the present version, but may differ in detail to address new problems 
or concerns.

<br><br>Each version is given a distinguishing version number. If the Program specifies 
a version number of this License which applies to it and \"any later version\", you 
have the option of following the terms and conditions either of that version or of 
any later version published by the Free Software Foundation. If the Program does 
not specify a version number of this License, you may choose any version ever 
published by the Free Software Foundation.

<br><br>10. If you wish to incorporate parts of the Program into other free programs whose 
distribution conditions are different, write to the author to ask for permission. 
For software which is copyrighted by the Free Software Foundation, write to the Free 
Software Foundation; we sometimes make exceptions for this. Our decision will be 
guided by the two goals of preserving the free status of all derivatives of our free 
software and of promoting the sharing and reuse of software generally.

<br><br>NO WARRANTY

<br><br>11. BECAUSE THE PROGRAM IS LICENSED FREE OF CHARGE, THERE IS NO WARRANTY FOR THE 
PROGRAM, TO THE EXTENT PERMITTED BY APPLICABLE LAW. EXCEPT WHEN OTHERWISE STATED 
IN WRITING THE COPYRIGHT HOLDERS AND/OR OTHER PARTIES PROVIDE THE PROGRAM \"AS IS\" 
WITHOUT WARRANTY OF ANY KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING, BUT NOT 
LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR 
PURPOSE. THE ENTIRE RISK AS TO THE QUALITY AND PERFORMANCE OF THE PROGRAM IS WITH 
YOU. SHOULD THE PROGRAM PROVE DEFECTIVE, YOU ASSUME THE COST OF ALL NECESSARY 
SERVICING, REPAIR OR CORRECTION.

<br><br>12. IN NO EVENT UNLESS REQUIRED BY APPLICABLE LAW OR AGREED TO IN WRITING WILL ANY 
COPYRIGHT HOLDER, OR ANY OTHER PARTY WHO MAY MODIFY AND/OR REDISTRIBUTE THE PROGRAM 
AS PERMITTED ABOVE, BE LIABLE TO YOU FOR DAMAGES, INCLUDING ANY GENERAL, SPECIAL, 
INCIDENTAL OR CONSEQUENTIAL DAMAGES ARISING OUT OF THE USE OR INABILITY TO USE THE 
PROGRAM (INCLUDING BUT NOT LIMITED TO LOSS OF DATA OR DATA BEING RENDERED INACCURATE 
OR LOSSES SUSTAINED BY YOU OR THIRD PARTIES OR A FAILURE OF THE PROGRAM TO OPERATE 
WITH ANY OTHER PROGRAMS), EVEN IF SUCH HOLDER OR OTHER PARTY HAS BEEN ADVISED OF 
THE POSSIBILITY OF SUCH DAMAGES.

<br><br>END OF TERMS AND CONDITIONS<br><br>
	
	
	</div>";
	
	$register_globals = (int) ini_get('register_globals');
	
	if ($register_globals == 1) echo "<br><br><a href=setup.php?checkmodus=1>&raquo; Setup starten</a><br><br>";
	else echo "<br><br><font class=error>Das Setup kann nicht gestartet werden. Bitte setzen sie register_globals = On [php.ini]</font><br><br>";
	
	$checkmodus = 0;	
}


// ---------------------------------- MODUS 1 | Versionscheck - Rechtecheck

if($checkmodus == 1)
{

	// -------------------------- VERSIONSCHECK

	if(version_compare(phpversion(), "4.1.0", "<") == 1)
	{
		$MSG[err] .= "Die PHP version des Servers ist zu alt (unter 4.1.0), bitte updaten Sie auf die aktuellste Version<br>";
	}

	// -------------------------- SCHREIBRECHTE
	
	$WRITEABLE = array("include/master.inc.php",
			   "include/generated",
			   "include/generated/categories",
			   "include/generated/articles",
			   "include/generated/templates",
			   "include/generated/logs",
			   "include/install",
			   "include/install/dbinstall_wdrop.sql",
			   "include/install/dbinstall_wodrop.sql",
			   "../files");

	foreach($WRITEABLE as $item)
	{
		if(is_dir($item))
		{
			if(!@is_writable($item."/."))
			{
				$MSG[err] .= "<font class=error>Fehler</font> | Das Verzeichniss <b>$item</b> ist nicht beschreib- oder ausführbar!<br>";
			}
		}elseif(is_file($item))
		{
			if(!@is_writable($item))
			{
				$MSG[err] .= "<font class=error>Fehler</font> | Die Datei <b>$item</b> ist nicht beschreibbar!<br>";
			}
		}else
		{
			$MSG[err] .= "<font class=error>Fehler</font> | Die Datei / Das Verzeichniss <b>$item</b> exisitert nicht, bitte erstellen Sie es. <br>";
		}
	}
}

if ($MSG[err]=="" && $checkmodus == 1)
{
	
	setuptitle("SETUP: SCHRITT 1 von 5");
	
	echo "<b>PHP-Versionscheck | Rechteüberprüfung</b>
		<br><br><font class=ok>ok</font> | PHP Version
		<br><font class=ok>ok</font> | Ordnerrechte
		<br><br>Bitte fahren Sie mit dem Setup fort.
		<br><br><a href=setup.php?checkmodus=2>&raquo; Weiter mit Schritt 2</a><br><br>";
		
}elseif($MSG[err]!="")
{
	
	setuptitle("SETUP: SCHRITT 1 von 5");
	
	echo "<b>PHP-Versionscheck | Rechteüberprüfung</b><br><br>".$MSG[err]."
	
	<br>Bitte beheben Sie die aufgelisteten Fehlern und starten Sie das Setup erneut.<br><br>
	<a href=setup.php?checkmodus=1>&raquo; Schritt 1 erneut durchführen</a><br><br>";
}


// ---------------------------------- MODUS 2 | master.inc.php - Datenbankcheck

if ($checkmodus == 2 && $send == 1)
{
	$h = @fopen("include/master.inc.php","r");
	$cont = fread($h,filesize("include/master.inc.php"));
	$cont = ereg_replace("(REX\[SERVER\].?\=.?\")[^\"]*","\\1".$serveraddress,$cont);
	$cont = ereg_replace("(REX\[SERVERNAME\].?\=.?\")[^\"]*","\\1".$serverbezeichnung,$cont);
	$cont = ereg_replace("(REX\[error_emailaddress\].?\=.?\")[^\"]*","\\1".$error_email,$cont);
	$cont = ereg_replace("(DB\[1\]\[HOST\].?\=.?\")[^\"]*","\\1".$mysql_host,$cont);
	$cont = ereg_replace("(DB\[1\]\[LOGIN\].?\=.?\")[^\"]*","\\1".$redaxo_db_user_login,$cont);
	$cont = ereg_replace("(DB\[1\]\[PSW\].?\=.?\")[^\"]*","\\1".$redaxo_db_user_pass,$cont);
	$cont = ereg_replace("(DB\[1\]\[NAME\].?\=.?\")[^\"]*","\\1".$dbname,$cont);
	fclose($h);
	
	$h = @fopen("include/master.inc.php","w+");
	if(fwrite($h,$cont,strlen($cont)) > 0)
	{
	}else
	{
		$err_msg = "<b>include/master.inc.php</b> konnte nicht geschrieben werden. Fehler nicht erkennbar !";
	}
	
	// -------------------------- DATENBANKZUGRIFF
	$link = @mysql_connect($mysql_host, $redaxo_db_user_login, $redaxo_db_user_pass);
	if(!$link)
	{
		$err_msg = "Es konnte keine Verbindung zur Datenbank hergestellte werden!";
	}elseif(!@mysql_select_db($dbname, $link))
	{
		$err_msg = "Die angegebene Datenbank konnte nicht geoeffnet werden!<br>";
	}elseif($link)
	{
		$DB[1][NAME] = $dbname;
		$DB[1][LOGIN] = $redaxo_db_user_login;
		$DB[1][PSW] = $redaxo_db_user_pass;
		$DB[1][HOST] = $mysql_host;
		
		$err_msg = "";
		$checkmodus = 3;
		$send = "";
	}
	@mysql_close($link);

}else
{
	$serveraddress = $REX[SERVER];
	$serverbezeichnung = $REX[SERVERNAME];
	$error_email = $REX[error_emailaddress];
	$dbname = $DB[1][NAME];
	$redaxo_db_user_login = $DB[1][LOGIN];
	$redaxo_db_user_pass = $DB[1][PSW];
	$mysql_host = $DB[1][HOST];	
}

if ($checkmodus == 2 )
{

	setuptitle("SETUP: SCHRITT 2 von 5");

	echo "<b>Schreiben der 'include/master.inc.php'</b><br><br>
		<table border=0 cellpadding=5 cellspacing=0 width=500>
		<form action=setup.php method=post>
		<input type=hidden name=checkmodus value=2>
		<input type=hidden name=send value=1>
		";
	
	if($err_msg!="") echo "<tr><td class=warning colspan=2>$err_msg</td></tr><tr><td></td></tr>";
	
	echo "
		<tr><td colspan=2>// ---- Allgemein Redaxo Einstellungen</td></tr>
		<tr><td width=200>Serverdomain [optional]</td><td><input type=text name=serveraddress value='$serveraddress' class=inp100></td></tr>
		<tr><td>Serverbezeichnung [optional]</td><td><input type=text name=serverbezeichnung value='$serverbezeichnung' class=inp100></td></tr>
		<tr><td>Fehler E-Mailadresse [optional]</td><td><input type=text name=error_email value='$error_email' class=inp100></td></tr>
		<tr><td colspan=2><br>// ---- Datenbankinformationen</td></tr>
		<tr><td>Name der Datenbank</td><td><input type=text class=inp100 value='$dbname' name=dbname></td></tr>
		<tr><td>MySQL Host</td><td><input type=text name=mysql_host value='$mysql_host' class=inp100></td></tr>
		<tr><td>Login</td><td><input type=text name=redaxo_db_user_login value='$redaxo_db_user_login' class=inp100></td></tr>
		<tr><td>Passwort</td><td><input type=text name=redaxo_db_user_pass value='$redaxo_db_user_pass' class=inp100></td></tr>
		<tr><td>&nbsp;</td><td valign=middle><input type=submit value='Weiter zu Schritt 3'></td></tr>
		</table>";
	
	echo "<br>";
}


// ---------------------------------- MODUS 3 | Datenbank anlegen ...

if ($checkmodus == 3 && $send == 1)
{

	if ($dbanlegen == 3)
	{
		// update
		$fname = "include/install/update2_6-2_7.sql";
		$h = fopen($fname,"r");
		$create = fread($h,filesize($fname));
		$link = mysql_connect($DB[1][HOST],$DB[1][LOGIN],$DB[1][PSW]);
		$lines = explode(";",$create);
		array_pop($lines);
		foreach($lines as $line)
		if(!mysql_db_query($DB[1][NAME],$line,$link))
		{
			$err_msg .= "Folgender Fehler tauchte beim Update auf. MySQL: ".mysql_error()."<br>";
		}
		
		
	}elseif ($dbanlegen == 2)
	{
		$TBLS = array("rex__article_comment" => 0,"rex__board" => 0,"rex__user" => 0,"rex__user_comment" => 0,
		"rex__user_mail" => 0,"rex_article" => 0,"rex_article_slice" => 0,"rex_article_type" => 0,
		"rex_category" => 0,"rex_email" => 0,"rex_file" => 0,"rex_modultyp" => 0,"rex_template" => 0,
		"rex_user" => 0, "rex_file_category" => 0);
		$gt = new sql;
		// $gt->debugsql = 1;
		$gt->setQuery("show tables");
		for ($i=0;$i<$gt->getRows();$i++,$gt->next())
		{
			$tblname = $gt->getValue("Tables_in_".$DB[1][NAME]);
			if (substr($tblname,0,4)=="rex_")
			{
				// echo $tblname."<br>";
				if (array_key_exists("$tblname",$TBLS))
				{
					$TBLS["$tblname"] = 1;
				}
			}
		}
		
		for ($i=0;$i<count($TBLS);$i++)
		{
			if (current($TBLS)!=1) $err_msg .= "Tabelle ".key($TBLS)." wurde nicht gefunden !<br>";
			next($TBLS);
		}
		
	}elseif($dbanlegen == 1)
	{
		$fname = "include/install/dbinstall_wdrop.sql";
		$h = fopen($fname,"r");
		$create = fread($h,filesize($fname));
		$link = mysql_connect($DB[1][HOST],$DB[1][LOGIN],$DB[1][PSW]);
		$lines = explode(";",$create);
		array_pop($lines);
		foreach($lines as $line)
		if(!mysql_db_query($DB[1][NAME],$line,$link))
		{
			$err_msg .= "Die Tabellen konnten nicht erstellt werden. MySQL: ".mysql_error()."<br>";
		}
	}elseif($dbanlegen == 0)
	{
		$fname = "include/install/dbinstall_wodrop.sql";
		$h = fopen($fname,"r");
		$create = fread($h,filesize($fname));
		$link = mysql_connect($DB[1][HOST],$DB[1][LOGIN],$DB[1][PSW]);
		$lines = explode(";",$create);
		array_pop($lines);
		foreach($lines as $line)
		if(!mysql_db_query($DB[1][NAME],$line,$link))
		{
			$err_msg .= "Die Tabellen konnten nicht erstellt werden. MySQL: ".mysql_error()."<br>";
		}
	}

	if ($err_msg == "")
	{
		$send = "";
		$checkmodus = 4;
	}
}

if ($checkmodus == 3)
{

	setuptitle("SETUP: SCHRITT 3 von 5");
	
	echo "<b>Datenbank anlegen</b><br><br>
		<table border=0 cellpadding=5 cellspacing=0 width=500>
		<form action=setup.php method=post>
		<input type=hidden name=checkmodus value=3>
		<input type=hidden name=send value=1>
		";
	
	if($err_msg!="") echo "<tr><td class=warning colspan=2>$err_msg<br>Bitte legen Sie die Datenbank neu an.</td></tr><tr><td></td></tr>";
	
	if ($dbanlegen == 1) $dbchecked1 = " checked";
	elseif ($dbanlegen == 2) $dbchecked2 = " checked";
	elseif ($dbanlegen == 3) $dbchecked3 = " checked";
	else $dbchecked0 = " checked";

	echo "
		<tr><td width=50 align=right><input type=radio name=dbanlegen value=0 $dbchecked0></td><td>Datenbank anlegen</td></tr>
		<tr><td width=50 align=right><input type=radio name=dbanlegen value=1 $dbchecked1></td><td>Datenbank anlegen und überschreiben falls vorhanden [Vorsicht]</td></tr>
		<tr><td align=right><input type=radio name=dbanlegen value=2 $dbchecked2></td><td>Datenbank existiert schon [weiter ohne Datenbankimport]</td></tr>
		<tr><td width=50 align=right><input type=radio name=dbanlegen value=3 $dbchecked3></td><td>Datenbankupdate von 2.6 auf 2.7 [Vorsicht]</td></tr>
		<tr><td>&nbsp;</td><td valign=middle><input type=submit value='Weiter zu Schritt 4'></td></tr>
		</table>";
	
	echo "<br>";
	
}


// ---------------------------------- MODUS 4 | User anlegen ...

if ($checkmodus == 4 && $send == 1)
{
	$err_msg = "";
	if ($noadmin != 1)
	{
		if ($redaxo_user_login == '')
		{
			$err_msg .= "Bitte geben Sie das Administratorlogin ein!<br>";
		}
		if ($redaxo_user_pass == '')
		{
			$err_msg .= "Bitte geben Sie das Administratorpasswort ein!<br>";
		}
		
		if($err_msg == "")
		{
			$ga = new sql;
			$ga->setQuery("select * from rex_user where login='$redaxo_user_login'");
			
			if ($ga->getRows()>0)
			{
				$err_msg = "Dieses Login existiert schon !";
			}else
			{	
				$insert = "INSERT INTO rex_user (name,login,psw,rights) VALUES ('Administrator','$redaxo_user_login','$redaxo_user_pass','structure[all]\r\narticle[5]\r\ntemplate[]\r\nuser[]\r\nnewsletter[]\r\nmodule[php]\r\nmodule[html]\r\nmodule[]\r\nspecials[]\r\n\r\ncommunity[]\r\nimport[]\n\rexport[]\n\radvancedMode[]\n\rstats[]')";
				$link = @mysql_connect($DB[1][HOST],$DB[1][LOGIN],$DB[1][PSW]);
				if(!@mysql_db_query($DB[1][NAME],$insert,$link))
				{
					$err_msg .= "Der Administrator konnte nicht angelegt werden.<br>";
				}
			}
		}
	}
	
	if ($err_msg == "")
	{
		$checkmodus = 5;
		$send = "";
	}

}

if ($checkmodus == 4)
{
	
	setuptitle("SETUP: SCHRITT 4 von 5");
	
	echo "<b>Administrator anlegen</b><br><br>
		<table border=0 cellpadding=5 cellspacing=0 width=500>
		<form action=setup.php method=post>
		<input type=hidden name=checkmodus value=4>
		<input type=hidden name=send value=1>
		";
	
	if($err_msg!="") echo "<tr><td class=warning colspan=2>$err_msg</td></tr><tr><td></td></tr>";
	
	if ($dbanlegen == 1) $dbchecked1 = " checked";
	elseif ($dbanlegen == 2) $dbchecked2 = " checked";
	else $dbchecked0 = " checked";

	echo "
		
		<tr><td>Login:</td><td><input type=text class=inp100 value=\"$redaxo_user_login\" name=redaxo_user_login></td></tr>
		<tr><td>Passwort:</td><td><input type=text class=inp100 value=\"$redaxo_user_pass\" name=redaxo_user_pass></td></tr>
		<tr><td align=right><input type=checkbox name=noadmin value=1></td><td>Keinen User anlegen</td></tr>
		<tr><td>&nbsp;</td><td valign=middle><input type=submit value='Weiter zu Schritt 5'></td></tr>
		</table>";
	
	echo "<br>";

}


// ---------------------------------- MODUS 5 | Setup verschieben ...

if ($checkmodus == 5)
{

	setuptitle("SETUP: SCHRITT 5 von 5");

	echo "<b>Herzlichen Glückwunsch zu Ihrem REDAXO! Bitte dennoch unbedingt unteren Text lesen !</b><br><br>
	
	Sie haben alle nötigen Einstellungen vorgenommen. Sollte der Zugang zu REDAXO dennoch nicht funktionieren so sollten Sie dieses 
	Setup neu aufrufen und Ihre Eingaben überprüfen. 
	
	<br><br><b class=error>!!! Sie sind noch nicht ganz fertig:</b><br>
	Wenn der Zugang zu REDAXO funktioniert dann löschen Sie bitte diese Setup datei [/redaxo/setup.php] damit keine anderen User 
	in Versuchung geraten Ihre Daten abzuändern. 
	
	<br><br><i>Weitere Vorgehensweise:</i><br>
	Sie haben im Moment eine REDAXO-Version ohne Beispielseiten oder fertigen Modulen/Templates. Um dies zu ändern können Sie auf 
	fertige Exporte [Webseiten] aus anderen Projekten zugreifen und diese in Ihr REDAXO übernehmen/importieren und mit diesen weiterarbeiten. 
	Sie finden diese Exporte auf der REDAXO [<a href=http://www.redaxo.de target=_blank>www.redaxo.de</a>] Webseite. Am besten Sie lesen 
	sich zuerst die '<b>_getting_started.txt</b>' durch.
	<br>Sie können sich aber auch erstmal in REDAXO einloggen und sich umsehen. 
	
	
	<br><br>Wir wünschen in jedem Fall viel Spass und erhoffen uns ein Feedback von Ihnen.
	
	<br><br>Ihr REDAXO Team
	
	<br><br>&raquo; <a href=index.php>Zum REDAXO Login</a> | &raquo; <a href=http://www.redaxo.de>http://www.redaxo.de</a>
	
	
	<br><br>";

}

echo "</font></td></tr></table>";

include $REX[INCLUDE_PATH]."/layout_redaxo/bottom.php";

?>