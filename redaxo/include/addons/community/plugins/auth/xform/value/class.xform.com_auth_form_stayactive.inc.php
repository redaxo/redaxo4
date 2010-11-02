<?php

class rex_xform_com_auth_form_stayactive extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{
		global $REX;

		if($REX['ADDON']['editme']['plugin_auth']['stay_active'] != 1)
			return;

		$l_label = 'eingeloggt bleiben';
		if (isset($this->elements[2])) $l_label = $this->elements[2];

		$checked = '';
		if (isset($this->elements[3]) && $this->elements[3] == 1) $checked = ' checked="checked"';

		$sa = rex_request($REX['ADDON']['editme']['plugin_auth']['request']['stay'],"int");
		if($sa == 1) $checked = ' checked="checked"';

		$form_output[] .= '
		<p class="formcheckbox form-com-auth-stayactive formlabel-'.$this->getName().'" id="'.$this->getHTMLId().'">
			<input type="checkbox" class="checkbox " name="'.$REX['ADDON']['editme']['plugin_auth']['request']['stay'].'" id="el_'.$this->getId().'" value="1" '.$checked.' />
			<label class="checkbox " for="el_'.$this->getId().'" >'.$l_label.'</label>
		</p>
		';

	}

	function getDescription()
	{
		return "com_auth_form_stayactive -> Beispiel: com_auth_form_stayactive|auth|eingeloggt bleiben:|0/1 angeklickt";
	}

}

?>