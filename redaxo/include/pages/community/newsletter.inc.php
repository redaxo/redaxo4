<?

title("<a href=index.php?page=community class=head>Community</a>: <a href=index.php?page=community&subpage=newsletter class=head>Newsletter</a>","");

$form_show = true;

if ($submit == 1)
{
	$errmsg = "";
	if ($from == "") $errmsg .= "Bitte geben Sie Ihre richtige E-Mail-Adresse an !<br>";
	if ($subject == "") $errmsg .= "Bitte tragen Sie ein Subject ein !<br>";
	if ($body == "") $errmsg .= "Bitte geben Sie den E-Mail-Inhalt ein !<br>";
	
	if ($errmsg == "")
	{
		
		// abschicken	
		$emails = new sql;
		$emails->setQuery("select email from rex__user where newsletter=1");
		
		for ($i=0;$i<$emails->getRows();$i++)
		{
			$from = stripslashes($from);
			$subject = stripslashes($subject);
			$body = stripslashes($body);
			$to = stripslashes($emails->getValue("rex__user.email"));
			
			$mail = new mime_mail();
			$mail->from = $from;
			$mail->headers = "Errors-To: ".$from;
			$mail->body = $body;
			// $mail->cc = stripslashes($cc);
			// $mail->bcc = stripslashes($bcc);
							
			$mail->prepare();
			$mail->to = $to;
			$mail->subject = $subject;
			$mail->send();
			
			$emails->next();	
		}
		
		$emails = new sql;
		$emails->setQuery("select email from rex_email");
		
		for ($i=0;$i<$emails->getRows();$i++)
		{
			$from = stripslashes($from);
			$subject = stripslashes($subject);
			$body = stripslashes($body);
			$to = stripslashes($emails->getValue("rex_email.email"));
			
			$mail = new mime_mail();
			$mail->from = $from;
			$mail->headers = "Errors-To: ".$from;
			$mail->body = $body;
			// $mail->cc = stripslashes($cc);
			// $mail->bcc = stripslashes($bcc);
							
			$mail->prepare();
			$mail->to = $to;
			$mail->subject = $subject;
			$mail->send();
			
			$emails->next();	
		}
				
		// no form
		$form_show = false;	
		
		$errmsg = "Ihre Eingaben wurden als Newsletter geschickt !";
	}		
}	

if ($errmsg != "") echo "<table border=0 cellpadding=5 cellspacing=1 width=770><tr><td colspan=2 class=warning>$errmsg</td></tr></table><br>";


echo "	<table border=0 cellpadding=5 cellspacing=1 width=770>
	<form action=index.php method=post>
	<input type=hidden name=page value=community>
	<input type=hidden name=subpage value=newsletter>
	<input type=hidden name=submit value=1>";


if ($form_show)
{

	$from = stripslashes(htmlentities($from));
	$subject = stripslashes(htmlentities($subject));
	$body = stripslashes(htmlentities($body));
		
	echo "
		<tr>
			<th class=dgrey width=100 align=left>E-Mail from</th>
			<td class=grey><input type=text name=from style='width:100%' size=20 value=\"".$from."\"></td>
		</tr>
		<tr>
			<th class=dgrey width=100 align=left>Subject</th>
			<td class=grey><input type=text name=subject style='width:100%' size=20 value=\"".$subject."\"></td>
		</tr>
		<tr>
			<th class=dgrey width=100 valign=top align=left>Body</th>
			<td class=grey><textarea name=body cols=30 rows=10 style='width:100%; height:300;'>".$body."</textarea></td>
		</tr>
		<tr>
			<th class=dgrey>&nbsp;</th>
			<td class=dgrey><input type=submit value=abschicken></td>
		</tr>
		</form>
		";
}

echo "</table>";



?>