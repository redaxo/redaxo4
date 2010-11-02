<?php

class rex_xform_emailtemplate
{


	function getTemplate($name)
	{
		$gt = rex_sql::factory();
		$gt->setQuery('select * from rex_xform_email_template where name="'.mysql_real_escape_string($name).'"');
		if ($gt->getRows()==1)
		{
			$b = $gt->getArray();
			return current($b);
		}
		return FALSE;
	}
	
	function replaceVars($template,$er = array())
	{
		global $REX;
		$er['REX_SERVER'] = $REX['SERVER'];
		$er['REX_ERROR_EMAIL'] = $REX['ERROR_EMAIL'];
		$er['REX_SERVERNAME'] = $REX['SERVERNAME'];
		$er['REX_NOTFOUND_ARTICLE_ID'] = $REX['NOTFOUND_ARTICLE_ID'];
		$er['REX_ARTICLE_ID'] = $REX['ARTICLE_ID'];
		foreach ($er as $search => $replace)
		{
			foreach($template as $k => $v)
			{
				$template[$k] = str_replace('###'. $search .'###', $replace, $template[$k]);
				$template[$k] = str_replace('***'. $search .'***', urlencode($replace), $template[$k]);
				$template[$k] = str_replace('+++'. $search .'+++', rex_xform_emailtemplate::makeSingleLine($replace), $template[$k]);
			}
		}
		return $template;
	}

	function makeSingleLine($str)
	{
		$str = str_replace("\n","",$str);
		$str = str_replace("\r","",$str);
		return $str;
	}
	
	function sendMail($template)
	{
		$mail = new rex_mailer();
		$mail->AddAddress($template["mail_to"], $template["mail_to_name"]);
		$mail->SetFrom($template["mail_from"], $template["mail_from_name"]);
		$mail->Subject = $template["subject"];
		$mail->Body = $template["body"];
		if($template["body_html"] != "") {
			$mail->AltBody = $template["body"];
			$mail->MsgHTML($template["body_html"]);
		}else {
			$mail->Body = strip_tags($template["body"]);
		}
		return $mail->Send();
	
	}
	
}