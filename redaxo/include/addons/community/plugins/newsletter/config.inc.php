<?php

/** 
 * Config . Zuständig für den Newsletter 
 * @author jan@kristinus
 * @version 1.0
 */ 

if ($REX["REDAXO"] 
		&& 
		$REX['USER'] 
		&& 
		($REX['USER']->isAdmin("rights","admin[]") || $REX['USER']->isValueOf("rights","community[admin]") || $REX['USER']->isValueOf("rights","community[setup]"))
)
{
	// Diese Seite noch extra einbinden
	$REX['ADDON']['community']['SUBPAGES'][] = array('plugin.newsletter','Newsletter');
}

$REX['ADDON']['NEWSLETTER_TEXT'] = FALSE;

// Feld festlegen, nicht lšschbar
$REX["ADDON"]["community"]["ff"][] = "last_newsletterid";

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

	$REX['REDAXO'] = $tmp_redaxo;

	return $mail->Send();

}