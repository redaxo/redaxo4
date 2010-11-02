<?php

class rex_xform_com_auth_form_password extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{
		global $REX;

		$l_label = 'Passwort';
		if (isset($this->elements[2])) $l_label = $this->elements[2];

		$form_output[] .= '
		<p class="formpassword form-com-auth-password formlabel-'.$this->getName().'" id="'.$this->getHTMLId().'">
			<label class="password " for="el_'.$this->getId().'" >'.$l_label.'</label>
			<input type="password" class="password " name="'.$REX['ADDON']['editme']['plugin_auth']['request']['psw'].'" id="el_'.$this->getId().'" value="" />
		</p>
		';

		return;

	}

	function getDescription()
	{
		return "com_auth_form_password -> Beispiel: com_auth_form_password|label|Passwort:";
	}

}

?>