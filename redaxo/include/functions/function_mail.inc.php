<?

function showemailextras($content)
{
	$content = eregi_replace("([_a-z0-9\.-]+)@([a-z0-9\.-]+)\.".
		"(net|com|gov|mil|org|edu|int|biz|info|name|pro|[A-Z]{2})".
		"($|[^a-z]{1})", "<a target=content href=\"index.php?article_id=66&FORM[ato]=\\1@\\2.\\3\">\\1@\\2.\\3</a>\\4 <a target=extra href=\"index.php?article_id=73&FORM[search]=email&FORM[email]=\\1@\\2.\\3\">+</a>", $content);
	return ($content);
}

function sendMail($to_email,$from_email,$subject,$message)
{
	// $subject = $to_email.":::".$subject;
	// $to_email = "jan@kristinus.de";
	mail($to_email,$subject,$message,"From: $from_email\nReply_to: $from_email\nX-Mailer: script-mailer\nContent-Type: text/plain; charset=iso-8859-1");
}


function validateEmail ($email)
{
	if ((!ereg(".+\@.+\..+", $email)) || (!ereg("^[a-zA-Z0-9_@.-]+$", $email))) return FALSE;
	else return TRUE;
	
}

?>