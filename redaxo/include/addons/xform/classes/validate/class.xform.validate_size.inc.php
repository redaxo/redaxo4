<?PHP

class rex_xform_validate_size extends rex_xform_validate_abstract 
{

	function enterObject(&$warning, $send, &$warning_messages)
	{
		if($send=="1")
		{		
			if(strlen($this->xaObjects[0]->getValue())!=$this->xaElements[3])
			{
				$warning["el_".$this->xaObjects[0]->getId()]=$this->params["error_class"];
				$warning_messages[] = $this->xaElements[4];
			}
		}
	}
	
	function getDescription()
	{
		return "size -> Länge der Eingabe muss gleich size sein, beispiel: validate|size|plz|6|warning_message";
	}
}
?>