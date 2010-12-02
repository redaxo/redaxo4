<?php

class rex_xform_fieldset extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{

		$class = '';
		if (isset($this->elements[3])  && $this->elements[3] != "")
		{
			$class = ' class="'.$this->elements[3].'" ';
		}

		$legend = "";
		if (isset($this->elements[2]) && $this->elements[2] != "")
		{
			$legend = '<legend id="'.$this->getHTMLId().'">' . $this->elements[2] . '</legend>';
		}

		if($this->params["first_fieldset"])
		{
			$this->params["first_fieldset"] = false;
			$form_output[] = $legend;

		}else
		{
			$form_output[] = '</fieldset><fieldset'.$class.' id="'.$this->getHTMLId().'">'.$legend;

		}

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