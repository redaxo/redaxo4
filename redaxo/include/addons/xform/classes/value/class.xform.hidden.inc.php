<?php

class rex_xform_hidden extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{
		if (isset($this->elements[3]) && $this->elements[3]=="REQUEST" && isset($_REQUEST[$this->elements[1]]))
		{
			$this->value = $_REQUEST[$this->elements[1]];
			$form_output[] = '<input type="hidden" name="'.$this->elements[1].'" value="'.$this->value.'" />';
		}else
		{
			$this->value = $this->elements[2];
			$email_elements[$this->elements[1]] = $this->value;
			if (!isset($this->elements[4]) || $this->elements[4] != "no_db") $sql_elements[$this->elements[1]] = $this->value;
		}
	}
	
	function getDescription()
	{
		return "
				hidden -> Beispiel: hidden|status|default_value
		<br />	hidden -> Beispiel: hidden|job_id|default_value|REQUEST|[no_db]
		";
	}
}

?>