<?php

/** 
 * Config . Zuständig für den Newsletter 
 * @author jan@kristinus
 * @version 0.9
 */ 


if ($REX["REDAXO"])
{
	// Diese Seite noch extra einbinden
	$REX['ADDON']['community']['subpages'][] = array('plugin.newsletter','Newsletter');

	// Im Setup aufnehmen - für Module.
	// $REX["ADDON"]["community"]["plugins"]["setup"]["modules"][] = array("guestbook","guestbook","1201 - COM-Module - Gästebuch");

	// EMails
	// $REX["ADDON"]["community"]["plugins"]["setup"]["emails"][] = array("guestbook","sendemail_guestbook","sendemail_guestbook","Community: Neuer Eintrag in Ihr Gästebuch", $REX['ERROR_EMAIL'], $REX['ERROR_EMAIL']);

}else
{




}

$REX['ADDON']['NEWSLETTER_TEXT'] = FALSE;

function rex_newsletter_sendmail($userinfo, $aid, $mail_reply, $mail_subject)
{

	global $REX;

	$tmp_redaxo = $REX['REDAXO'];

	$REX['REDAXO'] = true;

	 // ***** HTML VERSION KOMPLETT
	$REX_ARTICLE = new rex_article;
	$REX_ARTICLE->setCLang(0);
	$REX_ARTICLE->setArticleId($aid);
	$REX_ARTICLE->getContentAsQuery(TRUE);
	// $REX_ARTICLE->setTemplateId(xx);
	$REX['ADDON']['NEWSLETTER_TEXT'] = FALSE;
	$html_body = $REX_ARTICLE->getArticleTemplate();

	// ***** TEXT VERSION
	$REX_ARTICLE = new rex_article;
	$REX_ARTICLE->setCLang(0);
	$REX_ARTICLE->setArticleId($aid);
	$REX_ARTICLE->getContentAsQuery(TRUE);
	// $REX_ARTICLE->setTemplateId(xx);
	$REX['ADDON']['NEWSLETTER_TEXT'] = TRUE; // FILTERN VERSION KOMPLETT
	$text_body = $REX_ARTICLE->getArticle();
	$text_body = str_replace("<br />","<br />",$text_body);
	$text_body = str_replace("<p>","\n\n</p>",$text_body);
	$text_body = str_replace("<ul>","\n\n</ul>",$text_body);
	$text_body = preg_replace("#(\<)(.*)(\>)#imsU", "",  $text_body);
	$text_body = html_entity_decode($text_body);

	$REX['REDAXO'] = true;

	// ***** MAIL VERSAND
	
	// Allgemeine Initialisierung
	// $mail = new PHPMailer();
	$mail = new rex_mailer();
	$mail->AddAddress($userinfo["email"]);
	$mail->From = $mail_reply;
	$mail->FromName = $mail_reply;
	$subject = $mail_subject;

	// Subject		
	// Bodies
	// html

	foreach($userinfo as $k => $v)
	{
		$subject = str_replace( "###".$k."###",$v,$subject);
		$html_body = str_replace( "###".$k."###",$v,$html_body);
		$text_body = str_replace( "###".$k."###",$v,$text_body);
		$subject = str_replace( "###".strtoupper($k)."###",$v,$subject);
		$html_body = str_replace( "###".strtoupper($k)."###",$v,$html_body);
		$text_body = str_replace( "###".strtoupper($k)."###",$v,$text_body);
	}

	
	// text
	// echo "<pre>$text_body</pre>";
	
	$mail->Subject = $subject;
	$mail->AltBody = $text_body;
	$mail->Body = $html_body;
	$mail->Send();

	$REX['REDAXO'] = $tmp_redaxo;

}

?>