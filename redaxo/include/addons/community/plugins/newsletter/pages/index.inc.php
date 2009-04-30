<?php

/*
    var $Host     = "localhost";
    var $Mailer   = "smtp";
*/

$error = "";
$mail_reply = rex_request("mail_reply","string");
$mail_subject = rex_request("mail_subject","string");
$mail_aid = rex_request("mail_aid","int");
$mail_nlid = rex_request("mail_nlid","string");
if ($mail_nlid == "") $mail_nlid = date("YmdHi");
$test_email = rex_request("test_email","string");
$test_name = rex_request("test_name","string");
$test_firstname = rex_request("test_firstname","string");
$method = rex_request("method","string");
$method_all = rex_request("method_all","string","");

// TODO: wenn id gesetzt noch namen auslesen
$mail_name = "";

// ********************************************************* ALLE MAILS


if ($method_all == "all")
{
	if (
		$mail_reply != "" and 
		$mail_subject != "" and 
		$mail_nlid != "" and 
		$mail_aid > 0
	)
	{
		// ----- Mail an alle versenden
		// $mail_reply
		// $mail_subject
		// $mail_aid
		
		// ----- Info
		$msg = "Newsletter wurde komplett versandt!";
		
		// ----- eMails auslesen und versenden
		$nl = new rex_sql;
		// $nl->debugsql = 1;
		$nl->setQuery('select * from rex_com_user where last_newsletter_id<>"'.$mail_nlid.'" and status=1 LIMIT 50');
		
		if($nl->getRows()>0)
		{
			$msg = "".date("H:i:s")."h Bitte noch nicht abbrechen. Automatischer Reload. Es werden noch weitere E-Mails versendet";
			?><script>
			function win_reload(){ window.location.reload(); }
			setTimeout("win_reload()",5000); // Millisekunden 1000 = 1 Sek * 80
			</script><?php
			$msg .= "<br />An folgende E-Mails wurde der Newsletter versendet: ";
		}
		
		$up = new rex_sql;
		for ($i=0;$i<$nl->getRows();$i++)
		{
			$msg .= ", ".$nl->getValue("email");

			// ----- email miz mail_nlid aktualisieren
			$up->setQuery('update rex_com_user set last_newsletter_id="'.$mail_nlid.'" where id='.$nl->getValue("id"));

			$userinfo = array();
			$userinfo["login"] = $nl->getValue("login");
			$userinfo["email"] = $nl->getValue("email");
			$userinfo["name"] = $nl->getValue("name");
			$userinfo["firstname"] = $nl->getValue("firstname");

			rex_newsletter_sendmail($userinfo,$mail_aid, $mail_reply, $mail_subject, $to_code);

			$nl->next();	
		}


	}else
	{
		$msg = "Bitte geben Sie alle Daten ein!";
	}




// ********************************************************* TESTMAIL

}else if ($method == "start")
{
	// ----- Testmail verschicken

	if (
		$mail_reply != "" and 
		$mail_subject != "" and 
		$mail_aid > 0 and 
		$test_email != "" and 
		$test_name != "" and 
		$test_firstname != ""
	)
	{
		
		$userinfo = array();
		$userinfo["email"] = $test_email;
		$userinfo["name"] = $test_name;
		$userinfo["firstname"] = $test_firstname;

		rex_newsletter_sendmail($userinfo,$mail_aid, $mail_reply, $mail_subject);

		// Testmail verschicken..
		$msg = "Testmail wurde versandt!";
		
	}else
	{
		$method = "";
		$msg = "Bitte geben Sie alle Daten ein!";		
	}
}

if (isset($msg) && $msg != "")
{
	echo rex_warning($msg);		
}

?>

<table class="rex-table" cellpadding="5" cellspacing="1">
	<form action="index.php" method="get" name="REX_FORM">
	<input type="hidden" name="page" value="community" />
	<input type="hidden" name="subpage" value="plugin.newsletter" />
	<input type="hidden" name="method" value="start" />
	<tr>
		<th class="rex-icon">&nbsp;</th>
		<th colspan="2" style="font-size:12px;">
			<ul>
			<li>Artikel in REDAXO erstellen</li>
			<li>###email### / ###firstname### / ###name### als Platzhaler erlaubt</li>
			<li>Testmail schicken</li>
			<li>Wenn Testmail ok, dann Newsletter abschicken</li>
			</ul>
		</th>
	</tr>
</table><br />

<table class="rex-table" cellpadding="5" cellspacing="1">
	<tr>
		<th class="rex-icon">&nbsp;</th>
		<th colspan="2"><b>Newsletterdaten:</b></th>
	</tr>
	<tr>
		<td class="rex-icon">&nbsp;</td>
		<td width="200">Newsletterartikel:</td>
		<td>
			<div class="rex-wdgt">
			<div class="rex-wdgt-lnk">
			<p>
				<input type="hidden" name="mail_aid" id="LINK_1" value="<?php echo $mail_aid; ?>" />
				<input type="text" size="30" name="mail_name" value="<?php echo stripslashes(htmlspecialchars($mail_name)); ?>" id="LINK_1_NAME" readonly="readonly" />
				<a href="#" onclick="openLinkMap('LINK_1', '&clang=0');return false;" tabindex="23"><img src="media/file_open.gif" width="16" height="16" alt="Open Linkmap" title="Open Linkmap" /></a>
	
				<a href="#" onclick="deleteREXLink(1);return false;" tabindex="24"><img src="media/file_del.gif" width="16" height="16" title="Remove Selection" alt="Remove Selection" /></a>
			</p>
			</div>
			</div>
		</td>
	</tr>
	<tr>
		<td class=rex-icon>&nbsp;</td>
		<td>Absendeadresse:</td>
		<td><table class=rexbutton><tr><td><input type=text size=30 name='mail_reply' value="<?php echo stripslashes(htmlspecialchars($mail_reply)); ?>" class=inp100 ></td></tr></table></td>
	</tr>
	<tr>
		<td class=rex-icon>&nbsp;</td>
		<td>Betreff/Subject:<br>(Auch Platzhalter m&ouml;glich)</td>
		<td><table class=rexbutton><tr><td><input type=text size=30 name='mail_subject' value="<?php echo stripslashes(htmlspecialchars($mail_subject)); ?>" class=inp100 ></td></tr></table></td>
	</tr>
	<tr>
		<td class=rex-icon>&nbsp;</td>
		<td>NewsletterID:</td>
		<td><table class=rexbutton><tr><td><input type=text size=30 name='mail_nlid' value="<?php echo stripslashes(htmlspecialchars($mail_nlid)); ?>" class=inp100 ></td></tr></table></td>
	</tr>
	<tr>
		<th class=rex-icon>&nbsp;</th>
		<th colspan=2><b>Daten f&uuml;r Testmail eingeben:</b></th>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>E-Mail:</td>
		<td><table class=rexbutton><tr><td><input type=text size=30 name='test_email' value="<?php echo stripslashes(htmlspecialchars($test_email)); ?>" class=inp100 ></td></tr></table></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>Name:</td>
		<td><table class=rexbutton><tr><td><input type=text size=30 name='test_name' value="<?php echo stripslashes(htmlspecialchars($test_name)); ?>" class=inp100 ></td></tr></table></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>Vorname:</td>
		<td><table class=rexbutton><tr><td><input type=text size=30 name='test_firstname' value="<?php echo stripslashes(htmlspecialchars($test_firstname)); ?>" class=inp100 ></td></tr></table></td>
	</tr>
	<?php if ($method == "start") { ?>
	<tr>
		<td>&nbsp;</td>
		<td>Testmail ok ? Dann H&auml;kchen setzen <br>und Newsletter wird abgeschickt.</td>
		<td><table class=rexbutton style="width:30px;"><tr><td><input type=checkbox name="method_all" value="all" /></td></tr></table></td>
	</tr>
	<?php } ?>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td><input type=submit value="Mail/s verschicken"></td>
	</tr>
	</form>
</table>


