<?php

class rex_xform_datestamp extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{
		$format = "Y-m-d";
		if ($this->elements[2] != "") 
		  $format = $this->elements[2];

		$this->value = date($format);
		$email_elements[$this->elements[1]] = $this->value;

		if (!(isset($this->elements[3]) && $this->elements[3] == "no_db")) 
			$sql_elements[$this->elements[1]] = $this->value;

	}
	
	function getDescription()
	{
		return "datestamp -> Beispiel: datestamp|label|[Y-m-d]|[no_db]";
	}

}