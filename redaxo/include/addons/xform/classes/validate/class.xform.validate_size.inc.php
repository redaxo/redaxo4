<?php

class rex_xform_validate_size extends rex_xform_validate_abstract 
{

	function enterObject(&$warning, $send, &$warning_messages)
	{
		if($send=="1")
		{	
			
			// Wenn leer, dann alles ok
			if($this->xaObjects[0]->getValue() == "")
				return;
			
			if(strlen($this->xaObjects[0]->getValue())!=$this->xaElements[3])
			{
				$warning["el_".$this->xaObjects[0]->getId()]=$this->params["error_class"];
				$warning_messages[] = $this->xaElements[4];
			}
		}
	}
	
	function getDescription()
	{
		return "size -> Laenge der Eingabe muss gleich size sein, beispiel: validate|size|plz|6|warning_message";
	}
	
	function getDefinitions()
	{
		return array(
						'type' => 'validate',
						'name' => 'size',
						'values' => array(
             	array( 'type' => 'getName',   	'label' => 'Name' ),
              array( 'type' => 'text',    		'label' => 'Anzahl der Stellen'),
              array( 'type' => 'text',    		'label' => 'Fehlermeldung'),
              ),
						'description' => 'Hiermit wird ein Label ŸberprŸft ob es eine bestimmte Anzahl von Zeichen hat',
			);
	
	}
	
}