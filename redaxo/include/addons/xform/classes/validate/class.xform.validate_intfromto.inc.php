<?PHP

class rex_xform_validate_intfromto extends rex_xform_validate_abstract
{

	function enterObject(&$warning, $send, &$warning_messages)
	{
		if($send=="1")
		{
		
			$from = (int) $this->xaElements[3];
			$to = (int) $this->xaElements[4];
		
			foreach($this->xaObjects as $xoObject)
			{
				// echo '<p>Wert wird ¸berpr¸ft:';
				// echo "val: id:".$xoObject->getId()." value:".$xoObject->getValue()." 
				// elements:".print_r($xoObject->elements);
				// echo '</p>';
				$value = $xoObject->getValue();
				$value_int = (int) $value;
				
				if("$value" != "$value_int" || $value_int<$from || $value_int>$to)
				{
					$warning["el_" . $xoObject->getId()] = $this->params["error_class"];
					$warning_messages[] = $this->xaElements[5];
				}
			}
		}
	}
	
	
	function getDescription()
	{
		return "type -> pr¸ft auf zahlengrˆﬂe, grˆﬂer from, kleiner to: validate|intfromto|label|from|to|warning_message";
	}

	function getDefinitions()
	{
		return array(
						'type' => 'validate',
						'name' => 'intfromto',
						'values' => array(
             	array( 'type' => 'getlabel',   	'name' => 'Label' ),
              array( 'type' => 'text',    		'name' => 'Von'),
              array( 'type' => 'text',    		'name' => 'Bis'),
              array( 'type' => 'text',    		'name' => 'Fehlermeldung'),
              ),
						'description' => 'Hiermit wird ein Label überprüft ob es zwischen zwei Zahlen ist',
			);
	}
	
	
}