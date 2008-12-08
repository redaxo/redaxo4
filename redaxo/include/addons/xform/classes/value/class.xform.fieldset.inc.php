<?php

class rex_xform_fieldset extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{
	
		$cla = '';
		if (isset($this->elements[2])  && $this->elements[2] != "") $cla = ' class="'.$this->elements[2].'" ';
	
		$output = '
			<fieldset'.$cla.'>
			';
		
		if ($this->elements[1]) $output .= '<legend>' . $this->elements[1] . '</legend>';

		if($this->params["first_fieldset"])
		{
			$this->params["first_fieldset"] = false;
		}
		else
		{
			$output = '</fieldset>'. $output;
		}
      
		$form_elements[$this->id] = "";
		$form_output[] = $output;
	}
	
	function getDescription()
	{
		return "fieldset -> Beispiel: fieldset|Kundendaten|[class]";
	}
}

?>