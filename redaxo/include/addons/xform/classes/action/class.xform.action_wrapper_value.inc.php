<?php

class rex_xform_action_wrapper_value extends rex_xform_action_abstract
{
	function execute()
	{
		foreach($this->elements_sql as $key => $value)
		{
			if ($this->action["elements"][2] == $key)
			{
				$this->elements_sql[$key] = str_replace("###value###",$this->elements_sql[$key],$this->action["elements"][3]);
				break;
			}
		}
		return;
	}

	function getDescription()
	{
		return "action|wrapper_value|label|prefix###value###suffix";
	}
}

?>