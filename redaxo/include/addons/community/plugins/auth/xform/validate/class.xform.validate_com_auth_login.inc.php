<?PHP

class rex_xform_validate_com_auth_login extends rex_xform_validate_abstract 
{

	function enterObject(&$warning, $send, &$warning_messages)
	{

		$this->params["submit_btn_show"] = FALSE;

		$e = explode(";",$this->elements[2]);
		$s = array();
		foreach($e as $v)
		{
			$w = explode("=",$v);
			$label = $w[0];
			$value = trim(rex_request($w[1],"string",""));

			if($value == "")
			{
				$warning[] = 1;
				$warning_messages[] = $this->elements[4];
				return FALSE;
			}
			$s[] = '`'.$label.'`="'.$value.'"';
		}
		
		$loginquery = 'select * from rex_com_user where '.implode(" AND ",$s).' and status>0';

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
			
			// Clearlabel - for instance. delete activation key. only once available
			if(isset($this->elements[3]) && $this->elements[3] != "")
			{
				$clearlabel = $this->elements[3];
				$u = rex_sql::factory();
				// $u->debugsql = 1;
				$u->setTable('rex_com_user');
				$u->setWhere('id="'.$REX["COM_USER"]->getValue('id').'"');
				$u->setValue($this->elements[3],'');
				$u->update();
			
			}

		}else
		{
			// Nicht eingeloggt

			$warning[] = 1;
			$warning_messages[] = $this->elements[4];
		}
		// exit;
		return;

	}
	
	function getDescription()
	{
		return "com_auth_login -> prüft ob leer, beispiel: validate|com_auth_login|label1=request1;label2=request2|clearlabel|warning_message ";
	}
}
?>