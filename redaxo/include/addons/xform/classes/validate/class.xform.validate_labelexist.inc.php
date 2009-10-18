<?PHP

class rex_xform_validate_labelexist extends rex_xform_validate_abstract 
{

	function enterObject(&$warning, $send, &$warning_messages)
	{
		if($send=="1")
		{
		
			// optional, ein oder mehrere felder müssen ausgefüllt sein
			if(!isset($this->xaElements[3]) || $this->xaElements[3] == "")
				$minamount = 1;
			else
				$minamount = (int) $this->xaElements[4];

			if(!isset($this->xaElements[4]) || $this->xaElements[4] == "")
				$maxamount = 1000;
			else
				$maxamount = (int) $this->xaElements[4];


			// labels auslesen
			$fields = explode(",",$this->xaElements[2]);
			
			$value = 0;
			foreach($this->Objects as $o)
			{
				if (in_array($o->getDatabasefieldname(),$fields) && $o->getValue() != "") 
					$value++;
			}

			if ($value < $minamount || $value > $maxamount)
			{
				$warning_messages[] = $this->xaElements[5];
				
				foreach($this->Objects as $o)
				{
					if (in_array($o->getDatabasefieldname(),$fields))
					{
						$warning["el_" . $o->getId()] = $this->params["error_class"];
					}
				}
			}
		}
	}
	
	function getDescription()
	{
		return "labelexist -> mindestens ein feld muss ausgefüllt sein, example: validate|labelexist|label,label2,label3|[minlabels]|[maximallabels]|Fehlermeldung";
	}
}