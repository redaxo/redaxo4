<?php

class rex_xform_text extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{	
		$this->label = $this->elements[1];

		if ($this->value == "" && !$send)
		{
			if (isset($this->elements[3])) $this->value = $this->elements[3];
		}

		$wc = "";
		if (isset($warning["el_" . $this->getId()])) $wc = $warning["el_" . $this->getId()];
		
		$form_output[] = '
			<p class="formtext formlabel-'.$this->label.'">
				<label class="text ' . $wc . '" for="el_' . $this->id . '" >' . $this->elements[2] . '</label>
				<input type="text" class="text ' . $wc . '" name="FORM[' . 
				$this->params["form_name"] . '][el_' . $this->id . ']" id="el_' . $this->id . '" value="' . 
				htmlspecialchars(stripslashes($this->value)) . '" />
			</p>';
		$email_elements[$this->elements[1]] = stripslashes($this->value);
		if (!isset($this->elements[4]) || $this->elements[4] != "no_db") $sql_elements[$this->elements[1]] = $this->value;
	}
	
	function getDescription()
	{
		return "text -> Beispiel: text|label|Bezeichnung|defaultwert|[no_db]";
	}
	
	function getDefinitions()
	{
		return array(
						'type' => 'value',
						'name' => 'text',
						'values' => array(
             	array( 'type' => 'label',   'name' => 'Feld' ),
              array( 'type' => 'text',    'name' => 'Bezeichnung'),
              array( 'type' => 'text',    'name' => 'Defaultwert'),
							array( 'type' => 'no_db',   'name' => 'Datenbank',  'default' => 1),
						),
						'description' => 'Ein einfaches Textfeld als Eingabe',
						'dbtype' => 'text'
			);
	
	}
}

?>