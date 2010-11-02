<?php

class rex_xform_action_callback extends rex_xform_action_abstract
{

	function execute()
	{
	
		if(!isset($this->action["elements"][2]))
			return FALSE;
			
		$f = $this->action["elements"][2];
	
		$f($this);
	
		return;
	

	}

	function getDescription()
	{
		return "action|callback|function|";
	}

}

?>