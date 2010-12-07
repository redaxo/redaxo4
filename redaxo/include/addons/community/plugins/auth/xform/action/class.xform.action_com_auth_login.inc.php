<?php


class rex_xform_action_com_auth_login extends rex_xform_action_abstract
{
	
	function execute()
	{
		// action|db|rex_com_user|main_where
		
		global $REX;		

		if($this->params["main_where"] == "")
			return FALSE;
		
		$main_table = $this->params["main_table"];
		$main_where = $this->params["main_where"];

		$REX['COM_USER'] = new rex_com_user();

		if( 
			$REX['COM_USER']->checkQuery( 'select * from rex_com_user where '.$this->params["main_where"], array())
		  )
		{
			
			$REX['COM_USER']->sessionFixation();
		
			$session_key = $REX['COM_USER']->createSessionKey();
			$uu = rex_sql::factory();
			$uu->setQuery('update rex_com_user set session_key="'.$session_key.'" where '.$this->params["main_where"]);
		
			$REX['COM_USER']->setSessionVar('UID',$REX['COM_USER']->getValue('id'));
			$REX['COM_USER']->setSessionVar('SID', $session_key);
			$REX['COM_USER']->setSessionVar('STIME',time());

		}

	}

	function getDescription()
	{
		return "action|com_auth_login|";	// zum direkten einloggen, wenn where gesetzt ist
	}

}

?>