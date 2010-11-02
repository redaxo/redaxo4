<?php

class rex_xform_validate_size extends rex_xform_validate_abstract 
{

	function enterObject(&$warning, $send, &$warning_messages)
	{
		if($send=="1")
		{	
			
			// Wenn leer, dann alles ok
			if($this->obj_array[0]->getValue() == "")
				return;
			
			if(strlen($this->obj_array[0]->getValue())!=$this->getElement(3))
			{
				$warning[$this->obj_array[0]->getId()]=$this->params["error_class"];
				$warning_messages[$this->obj_array[0]->getId()] = $this->getElement(4);
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
							array( 'type' => 'getName', 'label' => 'Name' ),
							array( 'type' => 'text', 'label' => 'Anzahl der Zeichen'),
							array( 'type' => 'text', 'label' => 'Fehlermeldung'),
						),
						'description' => 'Hiermit wird ein Label überprüft ob es eine bestimmte Anzahl von Zeichen hat',
			);
	
	}
	
}