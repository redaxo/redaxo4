<?php

class rex_xform_fieldset extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{
	
		$cla = '';
		if (isset($this->elements[3])  && $this->elements[3] != "") 
			$cla = ' class="'.$this->elements[3].'" ';
	
		$output = '
			<fieldset'.$cla.' id="'.$this->getHTMLId().'">
			';
		
		if (isset($this->elements[2]) && $this->elements[2] != "") 
			$output .= '<legend id="el_'.$this->getId().'">' . $this->elements[2] . '</legend>';

		if($this->params["first_fieldset"])
		{
			$this->params["first_fieldset"] = false;
		}else
		{
			$output = '</fieldset>'. $output;
		}

		$form_elements[$this->getId()] = "";
		$form_output[] = $output;
	}
	
	function getDescription()
	{
		return "fieldset -> Beispiel: fieldset|label|Fieldsetbezeichnung|[class]";
	}

	function getDefinitions()
	{
		return array(
						'type' => 'value',
						'name' => 'fieldset',
						'values' => array(
							array( 'type' => 'name',	'value' => '' ),
							array( 'type' => 'text',	'label' => 'Bezeichnung'),
            			),
						'description' => 'hiermit kann man Bereiche in der Verwaltung erstellen.',
						'dbtype' => 'text'
			);
	}


}

?>