<?php

class rex_xform_com_auth_form_info extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{
		global $REX;

		$message = "";
		$layout = $this->elements[1];

		if(isset($_REQUEST[$REX['ADDON']['editme']['plugin_auth']['request']['name']]))
		{
			// loginversuch fehlgeschlagen
			if (isset($this->elements[2])) $message = $this->elements[2];	
		}else
		{
			// seite zum ersten mal aufgerufen
			if (isset($this->elements[3])) $message = $this->elements[3];	
		}

		if($message == "")
			return;
			
		$form_output[] .= str_replace('###message###',$message,$layout);

		return;

	}

	function getDescription()
	{
		return "com_auth_form_info -> Beispiel: com_auth_form_info|<p>###message###</p>|login_failed_info|please_login_info|";
	}

}

?>