<?PHP

class rex_xform_validate_labelexist extends rex_xform_validate_abstract 
{

	function enterObject(&$warning, $send, &$warning_messages)
	{
		if($send=="1")
		{
		
			// optional, ein oder mehrere felder müssen ausgefüllt sein
			if(!isset($this->xaElements[4]))
				$amount = 1;
			else
				$amount = (int) $this->xaElements[4];

			// labels auslesen
			$fields = explode(",",$this->xaElements[2]);
			
			$value = 0;
			foreach($this->Objects as $o)
			{
				if (in_array($o->getDatabasefieldname(),$fields) && $o->getValue() != "") 
					$value++;
			}

			if ($value < $amount)
				$warning_messages[] = $this->xaElements[3];
		}
	}
	
	function getDescription()
	{
		return "labelexist -> mindestens ein feld muss ausgefüllt sein, example: validate|atleastone|label,label2,label3|Fehlermeldung";
	}
}