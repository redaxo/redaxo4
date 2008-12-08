<?php

class rex_xform_captcha extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{
	
		// var_dump($this->params);
		global $REX; 
		
		
		require_once (realpath(dirname (__FILE__).'/../../ext/captcha/class.captcha_x.php'));

		if (isset($_REQUEST["captcha"]) && $_REQUEST["captcha"] == "show")
		{

			ob_end_clean();
			ob_end_clean();
			$server = &new captcha_x ();
			$server->handle_request ();
			exit;
		}

		$captcha = &new captcha_x ();
		
		if ( $send == 1 & $captcha->validate($this->value)) 
		{
			// Alles ist gut.
		}elseif($send==1)
		{
			// Error. Fehlermeldung ausgeben
			$this->params["warning"][] = $this->elements[2];
			$this->params["warning_messages"][] = $this->elements[2];
		}
	
		$link = rex_getUrl($this->params["article_id"],$this->params["clang"],array("captcha"=>"show"),"&");

		$form_output[] = '
			<p class="formcaptcha">
				<span>'.htmlspecialchars($this->elements[1]).'</span>
				<label class="captcha"><img 
					src="'.$link.'" 
					onclick="javascript:this.src=\''.$link.'&\'+Math.random();" 
					alt="CAPTCHA image" 
					/></label>
				<input maxlength="5" size="5" name="FORM['.$this->params["form_name"].'][el_'.$this->id.']" type="text" />
			</p>';
	}
	
	function getDescription()
	{
		return "captcha -> Beispiel: captcha|Beschreibungstext|Fehlertext";
	}
}

?>