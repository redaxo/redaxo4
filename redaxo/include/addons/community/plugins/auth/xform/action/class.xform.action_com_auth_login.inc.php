<?php

class rex_xform_action_com_auth_login extends rex_xform_action_abstract
{
	
	function execute()
	{

		if($this->getParam("main_where") == "")
			return FALSE;

		$loginquery = 'select * from rex_com_user where '.$this->getParam("main_where").' and status>0';

		if($this->params["debug"]) 
			echo $loginquery;

		$pagekey = 'comrex';
		$REX['COM_USER'] = new rex_login();
		$REX['COM_USER']->setSqlDb(1);
		$REX['COM_USER']->setSysID($pagekey);
		$REX['COM_USER']->setSessiontime(3000);
		$REX['COM_USER']->setUserID("rex_com_user.id");
		$REX['COM_USER']->setUserquery("select * from rex_com_user where id='USR_UID' and status>0");

		// Bei normalem Login
		$REX['COM_USER']->setLogin("11","22"); // quatsch setzen, login gefaked
		$REX['COM_USER']->setLoginquery($loginquery);

		if ($REX['COM_USER']->checkLogin())
		{
			// Eingeloggt
			if($this->params["debug"])
				echo "eingeloggt";

			return TRUE;

		}else
		{
			// Nicht eingeloggt
			if($this->params["debug"])
				echo "nicht eingeloggt";

			/* wenn fehler, dann das hier:
	
				$this->params["form_show"] = TRUE;
				$this->params["hasWarnings"] = TRUE;
				$this->params["warning_messages"][] = $this->params["Error-Code-InsertQueryError"];
	
			*/
			
			return FALSE;

		}

	}

	function getDescription()
	{
		return "action|com_auth_login|";
	}

}

?>