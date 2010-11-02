<?php

class rex_xform_validate_preg_match extends rex_xform_validate_abstract
{

	function enterObject(&$warning, $send, &$warning_messages)
	{
		if($send=="1")
		{
			$pm = $this->getElement(3);
			foreach($this->obj_array as $Object)
			{
				preg_match($pm, $Object->getValue(), $matches);

				if(count($matches) > 0 && current($matches) == $Object->getValue())
				{

				}else
				{
					$warning[$Object->getId()] = $this->params["error_class"];
					$warning_messages[] = $this->getElement(4);
				}
				 
			}
		}
	}

	function getDescription()
	{
		return "preg_match -> prüft über preg_match, beispiel: validate|preg_match|label|/[a-z]/i|warning_message ";
	}
}
?>