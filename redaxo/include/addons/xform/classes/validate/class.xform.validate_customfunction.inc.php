<?php

class rex_xform_validate_customfunction extends rex_xform_validate_abstract 
{

	function enterObject(&$warning, $send, &$warning_messages)
	{
		if($send=="1")
		{
		
			$l = $this->getElement(2);
			$f = $this->getElement(3);
			$p = $this->getElement(4);
			$o = $this->obj;
			
			foreach($this->obj_array as $Object)
			{
				if(function_exists($f))
				{
					if($f($l,$Object->getValue(),$p,$o))
					{
						$warning[$Object->getId()] = $this->params["error_class"];
						$warning_messages[$Object->getId()] = $this->getElement(5);
					}
				}else
				{
					$warning[$Object->getId()] = $this->params["error_class"];
					$warning_messages[$Object->getId()] = 'ERROR: customfunction "'.$f.'" not found';
				}
			}
		}
	}
  
	function getDescription()
	{
		return "customfunction -> prüft über customfunction, beispiel: validate|customfunction|label|functionname|weitere_parameter|warning_message";
	}
  
	function getDefinitions()
	{
		return array(
						'type' => 'validate',
						'name' => 'customfunction',
						'values' => array(
							array( 'type' => 'select_name', 'label' => 'Name'),
							array( 'type' => 'text',	'label' => 'Name der Funktion' ),
							array( 'type' => 'text', 	'label' => 'Weitere Parameter'),
							array( 'type' => 'text', 	'label' => 'Fehlermeldung'),
						),
						'description' => 'Mit eigener Funktion vergleichen',
			);
	
	}

}

?>