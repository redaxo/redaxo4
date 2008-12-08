<?PHP

class rex_xform_validate_type extends rex_xform_validate_abstract
{

	function enterObject(&$warning, $send, &$warning_messages)
	{
		if($send=="1")
		{
			$xoObject=$this->xaObjects[0];
			
			switch(trim($this->xaElements[3]))
			{
				case "int":
									$xsRegEx_int = "/^[0-9]+$/i";
									
									if(preg_match($xsRegEx_int, $xoObject->getValue())==0)
										$warning["el_" . $xoObject->getId()]=$this->params["error_class"];
									break;
									
				case "float":
									$xsRegEx_float = "/^([0-9]+|([0-9]+\.[0-9]+))$/i";
									
									if(preg_match($xsRegEx_float, $xoObject->getValue())==0)
										$warning["el_" . $xoObject->getId()]=$this->params["error_class"];
										
									break;
									
				case "numeric":
									if(!is_numeric($xoObject->getValue()))
										$warning["el_" . $xoObject->getId()]=$this->params["error_class"];
										
									break;
				case "string":	
									break;
				case "email":	
									$xsRegEx_email = "/^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,6}$/i";
									
									if(preg_match($xsRegEx_email, $xoObject->getValue())==0)
										$warning["el_" . $xoObject->getId()]=$this->params["error_class"];
									
									break;
				case "url":
									$xsRegEx_url = "(?:http:\/\/)[a-zA-Z0-9][a-zA-Z0-9._-]*\.(?:[a-zA-Z0-9][a-zA-Z0-9._-]*\.)*[a-zA-Z]{2,5}(?:\/[^\\\/\:\*\?\"<>\|]*)*(?:\/[a-zA-Z0-9_%,\.\=\?\-#&]*)*";
									
									if(preg_match("/^$xsRegEx_url$/", $xoObject->getValue())==0)
										$warning["el_" . $xoObject->getId()]=$this->params["error_class"];
									
									break;
				case "date":
									$xsRegEx_datum = "/^([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{2,4})$/i";
									
									if(preg_match($xsRegEx_datum, $xoObject->getValue())==0)
										$warning["el_" . $xoObject->getId()]=$this->params["error_class"];
										
									break;
									
				case "":
									break;
				default:			
									echo "Type ".$this->xaElements[3]." nicht definiert";
									$warning["el_" . $xoObject->getId()] = $this->params["error_class"];
									break;
			}
			if ($warning["el_" . $xoObject->getId()] != "") $warning_messages[] = $this->xaElements[4];
		}
	}
	
	
	function getDescription()
	{
		return "type -> prüft auf typ,beispiel: validate|type|plz|int(oder string,float,email,url,date)|warning_message";
	}
}
?>