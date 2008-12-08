<?php

class rex_xform_validate_existintable extends rex_xform_validate_abstract 
{

	function enterObject(&$warning, $send, &$warning_messages)
	{
		if($send=="1")
		{
			foreach($this->xaObjects as $xoObject)
			{
				$sql = 'select '.$this->xaElements[2].' from '.$this->xaElements[3].' WHERE '.$this->xaElements[4].'="'.$xoObject->getValue().'" LIMIT 2';
				$cd = new rex_sql;
				// $cd->debugsql = 1;
				$cd->setQuery($sql);
				if ($cd->getRows()!=1)
				{
					$warning["el_" . $xoObject->getId()] = $this->params["error_class"];
					$warning_messages[] = $this->xaElements[5];
				}
			}
		}
	}
	
	function getDescription()
	{
		return "existintable -> prüft ob vorhanden, beispiel: validate|existintable|label|tablename|feldname|warning_message";
	}
}

?>