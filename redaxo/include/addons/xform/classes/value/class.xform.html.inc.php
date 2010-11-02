<?php

class rex_xform_html extends rex_xform_abstract
{

	function enterObject(&$email_elements,&$sql_elements,&$warning,&$form_output,$send = 0)
	{
		// --- OLD ... 
		if(!isset($this->elements[2])) 
		{
			$form_output[] = $this->elements[1];
		}
		
		// --- New
		else{
			$form_output[] = $this->elements[2];
		}
		
		
	}
	
	function getDescription()
	{
		return htmlspecialchars(stripslashes('html -> Beispiel: html|<div class="block">'));
	}
	
	function getDefinitions()
	{
		return array(
						'type' => 'value',
						'name' => 'html',
						'values' => array(
									array( 'type' => 'name',   'label' => 'Feld' ),
									array( 'type' => 'textarea',    'label' => 'HTML'),
		        		),
						'description' => 'Nur für die Ausgabe gedacht',
						'dbtype' => 'text'
					);

	}
	
	
}

?>