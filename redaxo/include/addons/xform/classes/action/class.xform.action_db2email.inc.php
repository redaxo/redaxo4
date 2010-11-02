<?php

class rex_xform_action_db2email extends rex_xform_action_abstract
{
	
	function execute()
	{

		global $REX;

		$template_name = $this->action["elements"][2];

		if($etpl = rex_xform_emailtemplate::getTemplate($template_name))
		{

			// ----- find mailto
			$mail_to = $REX['ERROR_EMAIL']; // default
			
			// finde email label in list
			if (isset($this->action["elements"][3]) && $this->action["elements"][3] != "")
			{
				foreach($this->elements_email as $key => $value)
					if ($this->action["elements"][3]==$key)
					{
						$mail_to = $value;
						break;
					}
			}
			
			// ---- fix mailto from definition
			if (isset($this->action["elements"][4]) && $this->action["elements"][4] != "") 
				$mail_to = $this->action["elements"][4];
		
			$etpl = rex_xform_emailtemplate::replaceVars($etpl,$this->elements_email);
		
			$etpl['mail_to'] = $mail_to;
			$etpl['mail_to_name'] = $mail_to;
			if(!rex_xform_emailtemplate::sendMail($etpl))
			{
				echo "Fehler beim E-Mail Versand";
				return FALSE;
			}
			
			return TRUE;
		
		}

		return FALSE;

	}

	function getDescription()
	{

		return "action|db2email|namekey|emaillabel|[email@domain.de]";

	}

}

?>