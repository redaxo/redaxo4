<?php

class rex_xform_validate_unique extends rex_xform_validate_abstract 
{

	function enterObject(&$warning, $send, &$warning_messages)
	{
		if($send=="1")
		{
			foreach($this->xaObjects as $xoObject)
			{
				$sql = 'select '.$this->xaElements[2].' from '.$this->params["main_table"].' WHERE '.$this->xaElements[2].'="'.$xoObject->getValue().'" LIMIT 1';
				$cd = new rex_sql;
				$cd->setQuery($sql);
				if ($cd->getRows()>0)
				{
					$warning["el_" . $xoObject->getId()] = $this->params["error_class"];
					$warning_messages[] = $this->xaElements[3];
				}
			}
		}
	}
	
	function getDescription()
	{
		return "unique -> prüft ob unique, beispiel: validate|unique|user.name|Dieser Name existiert schon";
	}
}

?>