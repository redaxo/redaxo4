<?php

class rex_xform_validate_labelexist extends rex_xform_validate_abstract 
{

	function enterObject(&$warning, $send, &$warning_messages)
	{
		if($send=="1")
		{
		
			// optional, ein oder mehrere felder müssen ausgefüllt sein
			if($this->getElement(3) == "")
				$minamount = 1;
			else
				$minamount = (int) $this->getElement(3);

			if($this->getElement(4) == "")
				$maxamount = 1000;
			else
				$maxamount = (int) $this->getElement(4);


			// labels auslesen
			$fields = explode(",",$this->getElement(2));
			
			$value = 0;
			foreach($this->obj as $o)
			{
				if (in_array($o->getDatabasefieldname(),$fields) && $o->getValue() != "") 
					$value++;
			}

			if ($value < $minamount || $value > $maxamount)
			{
				$warning_messages[] = $this->getElement(5);
				
				foreach($this->obj as $o)
				{
					if (in_array($o->getDatabasefieldname(),$fields))
					{
						$warning[$o->getId()] = $this->params["error_class"];
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