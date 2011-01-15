<?php

class rex_xform_validate_type extends rex_xform_validate_abstract
{

	function enterObject(&$warning, $send, &$warning_messages)
	{
		if($send=="1")
		{

			$Object = $this->obj_array[0];

			$error = FALSE;

			switch(trim($this->getElement(3)))
			{

				case "int":
					$xsRegEx_int = "/^\-?[0-9]+$/i";
					if(preg_match($xsRegEx_int, $Object->getValue())==0){ $error = TRUE; }
					elseif($Object->getValue() < -2147483648) { $error = TRUE; }
					elseif($Object->getValue() > 2147483648) { $error = TRUE; }
					break;

				case "float":
					$float = $this->getDefaultTypeValue("float");
					if($this->getElement(4) != "") { $float = $this->getElement(4); }
					$float = explode(",",$float);
					$float_0 = (int) $float[0];
					$float_1 = (int) $float[1];
					$xsRegEx_float = '/^([0-9]{0,'.$float_0.'}|([0-9]{1,'.$float_0.'}\.[0-9]{1,'.$float_1.'}))$/i';
					if(preg_match($xsRegEx_float, $Object->getValue())==0) { $error = TRUE; }
					break;

				case "email":
					$xsRegEx_email = "/^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,6}$/i";
					// "/^[\w.+-]{2,}\@[\w.-]{2,}\.[a-z]{2,6}$/";
					if(preg_match($xsRegEx_email, $Object->getValue())==0) { $error = TRUE; }
					break;

				case "url":
					$xsRegEx_url = '/^(?:http:\/\/)[a-zA-Z0-9][a-zA-Z0-9._-]*\.(?:[a-zA-Z0-9][a-zA-Z0-9._-]*\.)*[a-zA-Z]{2,5}(?:\/[^\\/\:\*\?\"<>\|]*)*(?:\/[a-zA-Z0-9_%,\.\=\?\-#&]*)*$'."/'";
					if(preg_match($xsRegEx_url, $Object->getValue())==0) { $error = TRUE; }
					break;

				case "varchar":
					$char = $this->getDefaultTypeValue("varchar");
					if($this->getElement(4) != "") { $char = (int) $this->getElement(4); }
					if(strlen($Object->getValue()) > $char) { $error = TRUE; }

				case "text":
					break;

				default:
					echo "Type ".$this->elements[3]." nicht definiert";
					$error = TRUE;
					break;
			}

			if ($error)
			{
				$warning[$Object->getId()]=$this->params["error_class"];
				$warning_messages[$Object->getId()] = $this->getElement(5);
					
			}

		}
	}

	function getDefaultTypeValue($type)
	{
		switch($type)
		{
			case("varchar"): return '255';
			case("float"): return '7,2';
		}
		return '';
	}

	function getDescription()
	{
		return "type -> prüft auf typ,beispiel: validate|type|label|int(oder float,email,url,varchar,text)|opt.extras|Fehlermeldung|";
	}

	function getDefinitions()
	{
		return array(
            'type' => 'validate',
            'name' => 'type',
            'values' => array(
		array( 'type' => 'select_name',    'label' => 'Name' ),
		array( 'type' => 'select',  'label' => 'Feldtyp', 'definition' => 'int=int;float=float;email=email;varchar=varchar;text=text', 'default'=>'text'),
		array( 'type' => 'text',    'label' => 'Extra (varchar(x=255) float(x=9,2)'),
		array( 'type' => 'text',    'label' => 'Fehlermeldung'),
		),
            'description' => 'Mit dieser Definition wird das Feld nach entsprechendem Typ geprüft und in der Datenbank festgelegt',
            'dbtype' => 'text'
            );
	}



}