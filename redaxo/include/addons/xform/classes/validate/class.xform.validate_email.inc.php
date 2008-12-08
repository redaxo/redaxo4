<?PHP

class rex_xform_validate_email extends rex_xform_validate_abstract 
{

	function enterObject(&$warning, $send, &$warning_messages)
	{
		if($send=="1")
			foreach($this->xaObjects as $xoObject)
			{
				if ((!ereg(".+\@.+\..+", $xoObject->getValue())) || (!ereg("^[a-zA-Z0-9_@.-]+$", $xoObject->getValue())))
				{
					$warning["el_" . $xoObject->getId()] = $this->params["error_class"];
					$warning_messages[] = $this->xaElements[3];
				}
			}
	}
	
	function getDescription()
	{
		return "email -> prüft ob email, beispiel: validate|email|emaillabel|warning_message ";
	}
}

?>