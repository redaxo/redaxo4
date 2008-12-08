<?php

class rex_xform_resetbutton extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{	
		$this->label = $this->elements[1];
		$this->value = $this->elements[2];

		$css_class = "";
		if (isset($this->elements[3]) && $this->elements[4] != "") $css_class = $this->elements[3];
		$wc = $css_class;
	
       	$form_output[] = '
				<p class="formsubmit formlabel-'.$this->label.'">
				<label class="text ' . $wc . '" for="el_' . $this->id . '" >&nbsp;</label>
				<input type="reset" class="submit ' . $wc . '" id="el_' . $this->id . '" value="' . 
				htmlspecialchars(stripslashes($this->value)) . '" />
				</p>';
	
	}
	
	function getDescription()
	{
		return "submit -> Beispiel: submit|label|value|cssclassname";
	}
}

?>