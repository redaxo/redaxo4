<?PHP

class rex_xform_validate_notEmpty extends rex_xform_validate_abstract 
{

	function enterObject(&$warning, $send, &$warning_messages)
	{
		if($send=="1")
		{
			foreach($this->obj_array as $Object)
			{
				// echo '<p>Wert wird überprüft:';
				// echo "val: id:".$xoObject->getId()." value:".$xoObject->getValue()." elements:".print_r($xoObject->elements);
				// echo '</p>';
			
				if($Object->getValue() == "")
				{
					$warning[$Object->getId()] = $this->params["error_class"];
					$warning_messages[$Object->getId()] = $this->getElement(3);
				}
			}
		}
	}
	
	function getDescription()
	{
		return "notEmpty -> prüft ob leer, beispiel: validate|notEmpty|label|warning_message ";
	}
	
	function getDefinitions()
	{
		return array(
					'type' => 'validate',
					'name' => 'notEmpty',
					'values' => array(
						array( 'type' => 'select_name',	'label' => 'Name' ),
						array( 'type' => 'text',		'label' => 'Fehlermeldung'),
					),
					'description' => 'Hiermit wird ein Label überprüft ob es gesetzt ist',
				);
	
	}
	
	
}
?>