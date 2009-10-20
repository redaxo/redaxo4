<?php

class rex_xform_textarea extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{		
		if ($this->value == "" && !$send)
		{
			if (isset($this->elements[3])) $this->value = $this->elements[3];
		}

		$wc = "";
		if (isset($warning["el_" . $this->getId()])) $wc = $warning["el_" . $this->getId()];
		
		$form_output[] = '
		<p class="formtextarea">
			<label class="textarea ' . $wc . '" for="el_' . $this->id . '" >' . $this->elements[2] . '</label>
			<textarea class="textarea ' . $wc . '" name="FORM[' . $this->params["form_name"] . '][el_' . $this->id . ']" id="el_' . $this->id . '" cols="80" rows="10">' . htmlspecialchars(stripslashes($this->value)) . '</textarea>
		</p>';

		$email_elements[$this->elements[1]] = stripslashes($this->value);
		if (!isset($this->elements[4]) || $this->elements[4] != "no_db") $sql_elements[$this->elements[1]] = $this->value;
	}
	
	function getDescription()
	{
		return "textarea -> Beispiel: textarea|label|FieldLabel|default|[no_db]";
	}
	
	function getDefinitions()
	{
		return array(
						'type' => 'value',
						'name' => 'textarea',
						'values' => array(
             	'label' => array('Feld'),
              'text' => array('Bezeichnung'),
              'text' => array('Defaultwert'),
							'no_db' => array('Datenbank',1),
						),
						'description' => 'Ein Textfeld fŸr mehrzeilige Eingaben',
			);
	
	}
}

?>