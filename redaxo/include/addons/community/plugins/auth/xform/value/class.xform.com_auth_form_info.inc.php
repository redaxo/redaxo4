<?php

class rex_xform_com_auth_form_info extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{
		global $REX;

		$message = "";
		
		$info = rex_request('rex_com_auth_info',"int");
		
		$messages = array();
		$messages[0] = $this->getElement(2);
		$messages[1] = $this->getElement(3);
		$messages[2] = $this->getElement(4);
		$messages[3] = $this->getElement(5);
		$messages[4] = $this->getElement(6);
		
		$message = @$messages[$info];

		if($message != "") 
		{
			$this->params["form_output"][$this->getId()] = '
				<p class="formcom_auth_form_info formlabel-'.$this->getName().'" id="'.$this->getHTMLId().'">
					'.$message.'
				</p>';
		}
	
		return;

	}

	function getDescription()
	{
		return "com_auth_form_info [0 - nichts / 1 - logout / 2 - failed login / 3 - logged in] -> Beispiel: com_auth_form_info|label|msg_welcome|msg_logout|msg_loginfailed|msg_login|msg_sessiontimeout";
	}

}

?>