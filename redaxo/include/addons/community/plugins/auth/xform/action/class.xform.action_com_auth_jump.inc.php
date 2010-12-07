<?php


class rex_xform_action_com_auth_jump extends rex_xform_action_abstract
{
	
	function execute()
	{
		
		$rex_com_auth_jump = rex_request('rex_com_auth_jump','string');

		if($rex_com_auth_jump != "")
		{
			ob_end_clean();
			header('Location: http://'.$REX["SERVER"].'/'.rex_com_auth_urldecode($rex_com_auth_jump));
			exit;
		
		}

	}

	function getDescription()
	{
		return "action|com_auth_jump|";
	}

}

?>