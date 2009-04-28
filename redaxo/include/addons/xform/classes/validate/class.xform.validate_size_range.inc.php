<?php

class rex_xform_validate_size_range extends rex_xform_validate_abstract 
{

	function enterObject(&$warning, $send, &$warning_messages)
	{
		if($send=="1")
		{		
			
			// Wenn leer, dann alles ok
			if($this->xaObjects[0]->getValue() == "")
				return;
			
			$w = FALSE;
			
			$minsize = -1;
			if($this->xaElements[3] != "")
				$minsize = (int) $this->xaElements[3];

			$maxsize = -1;
			if($this->xaElements[4] != "")
				$maxsize = (int) $this->xaElements[4];
				
			$size = strlen($this->xaObjects[0]->getValue());
			
			if($minsize > -1 && $minsize > $size)
				$w = TRUE;

			if($maxsize > -1 && $maxsize < $size)
				$w = TRUE;
				
			if($w)
			{
				$warning["el_".$this->xaObjects[0]->getId()]=$this->params["error_class"];
				$warning_messages[] = $this->xaElements[5];
			}
		}
	}
	
	function getDescription()
	{
		return "size -> Laenge der Eingabe muss mindestens und/oder maximal sein, beispiel: validate|size_range|label|[minsize]|[maxsize]|Fehlermeldung";
	}
}