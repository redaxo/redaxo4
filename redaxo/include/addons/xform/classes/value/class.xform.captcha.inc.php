<?php

class rex_xform_captcha extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{

		global $REX;

		require_once (realpath(dirname (__FILE__).'/../../ext/captcha/class.captcha_x.php'));

		$captcha = new captcha_x ();
		$captchaRequest = rex_request('captcha', 'string');

		if ($captchaRequest == "show")
		{
			while(@ob_end_clean());
			$captcha->handle_request();
			exit;
		}

		$wc = "";
		// hier bewusst nur ein "&" (konditionales und, kein boolsches und!)
		if ( $send == 1 & $captcha->validate($this->value))
		{
			// Alles ist gut.
			// *** Captcha Code leeren, nur einmal verwenden, doppelt versand des Formulars damit auch verhindern
			if (isset($_SESSION['captcha'])) 
			{
				unset($_SESSION['captcha']);
			}
		}elseif($send==1)
		{
			// Error. Fehlermeldung ausgeben
			$this->params["warning"][$this->getId()] = $this->elements[2];
			$this->params["warning_messages"][$this->getId()] = $this->elements[2];
			$wc = $this->params["error_class"];
		}

		$link = rex_getUrl($this->params["article_id"],$this->params["clang"],array("captcha"=>"show"),"&");

		if ($wc != '')
			$wc = ' '.$wc;
			
		$form_output[] = '
			<p class="formcaptcha" id="'.$this->getHTMLId().'">
				<label class="captcha' . $wc . '" for="el_' . $this->id . '">'.htmlspecialchars($this->elements[1]).'</label>
				<span class="as-label' . $wc . '"><img  src="'.$link.'" onclick="javascript:this.src=\''.$link.'&\'+Math.random();" alt="CAPTCHA image" /></span>
				<input class="captcha' . $wc . '" maxlength="5" size="5" id="el_' . $this->id . '" name="'.$this->getFormFieldname().'" type="text" />
			</p>';
		// Ende
	}

	function getDescription()
	{
		return "captcha -> Beispiel: captcha|Beschreibungstext|Fehlertext";
	}

}

?>