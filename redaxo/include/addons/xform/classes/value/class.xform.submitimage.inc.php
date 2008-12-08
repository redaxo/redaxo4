<?php

class rex_xform_submitimage extends rex_xform_abstract
{

	function loadParams($params = array(),$elements = array())
	{
		$params["submit_btn_show"] = FALSE;
		$this->params = $params;
		$this->elements = $elements;
	}

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{	
		$this->label = $this->elements[1];
		$this->value = $this->elements[2];
		$src = $this->elements[3];
	
       	$form_output[] = '
			<p class="formsubmit formlabel-'.$this->label.'">
				<label class="submitimage " for="el_' . $this->id . '" >&nbsp;</label>
				<input type="image" src="'.$src.'" class="submit " name="FORM[' . 
				$this->params["form_name"] . '][el_' . $this->id . ']" id="el_' . $this->id . '" value="' . 
				htmlspecialchars(stripslashes($this->value)) . '" />
			</p>';
		$email_elements[$this->elements[1]] = stripslashes($this->value);
		if ($this->elements[4] != "no_db") $sql_elements[$this->elements[1]] = $this->value;
	}
	
	function getDescription()
	{
		return "submitimage -> Beispiel: submitimage|label|value|imgsrc|[no_db]";
	}
}

?>