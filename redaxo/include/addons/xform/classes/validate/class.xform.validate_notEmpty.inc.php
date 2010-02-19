<?PHP

class rex_xform_validate_notEmpty extends rex_xform_validate_abstract 
{

	function enterObject(&$warning, $send, &$warning_messages)
	{
		if($send=="1")
		{
			foreach($this->xaObjects as $xoObject)
			{
				// echo '<p>Wert wird überprüft:';
				// echo "val: id:".$xoObject->getId()." value:".$xoObject->getValue()." elements:".print_r($xoObject->elements);
				// echo '</p>';
			
				if($xoObject->getValue() == "")
				{
					$warning["el_" . $xoObject->getId()] = $this->params["error_class"];
					if (!isset($this->xaElements[3])) $this->xaElements[3] = "";
					$warning_messages[] = $this->xaElements[3];
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
						array( 'type' => 'getName',   'label' => 'Name' ),
						array( 'type' => 'text',    'label' => 'Fehlermeldung'),
					),
					'description' => 'Hiermit wird ein Label ŸberprŸft ob es gesetzt ist',
				);
	
	}
	
	
}
?>