<?php

class rex_com_user
{

	function rex_com_user()
	{
		if(!isset($_SESSION))
		{
			session_start();
		}
	
		$this->setSystemID("comrex");
		$this->setCookieDays(14); // in days
		$this->setMessage();
		$this->setValues(array());

	}

	function setValues($info = array())
	{
		$this->info = $info;
	}

	function getValue($key, $default = "")
	{
		if(isset($this->info[$key]))
			return $this->info[$key];
		return $default;
	}

	function setValue($key, $value)
	{
		$this->info[$key] = $value;
	}
	
	function setSystemID($system_id)
	{
		$this->system_id = $system_id;
	}

	function getSystemID()
	{
		return $this->system_id;
	}


	function logout()
	{
		// unset Session / Cookie / setMessage
		$this->deleteSessionVars();
		$this->deleteCookieVars();
		$this->setMessage('logout');
	}

	function setMessage($message = "", $detailinfo = "")
	{
		$messages = array(1=>'logout', 3=>'login_ok', 2=>'login_failed', 0=>'welcome', 4=>'session_expired', ); // 0 - nichts / 1 - logout / 2 - failed login / 3 - logged in

		if(!in_array($message,$messages))
			$message = 'welcome';
		$this->message = $message;
		return $message;
	}

	function getMessage()
	{
		return $this->message;
	}

	function checkQuery(
			$query = "",
			$labels = array()
		)
	{
	
		global $REX;

		$user_query = rex_sql::factory();		
		// $user_query->debugsql = 1;
		foreach($labels as $k => $v)
		{
			$query = str_replace($k,mysql_real_escape_string($v),$query);
		}
		$user_query->setQuery($query);
		if($user_query->getRows()==1)
		{
			$info = $user_query->getArray();
			$this->setValues($info[0]);
			$this->setMessage('login_ok');
			return TRUE;
		}else
		{
			$this->setMessage('login_failed');
			return FALSE;
		}

	}

  


	// ---------- SESSION
	function deleteSessionVars()
	{
		$_SESSION[$this->getSystemID()] = array();
	}

	function setSessionVar($varname, $value)
	{
		$_SESSION[$this->getSystemID()][$varname] = $value;
	}
	
	function getSessionVar($varname, $default = '')
	{
		if (isset ($_SESSION[$this->getSystemID()][$varname]))
			return $_SESSION[$this->getSystemID()][$varname];
	
		return $default;
	}

	function sessionFixation()
	{
		if (function_exists('session_regenerate_id'))
		{
			session_regenerate_id();
		}
	}

	function createSessionKey()
	{
		return sha1(time().rand(0,10000));
	}

	// ---------- COOKIES
	function setCookieDays($cookie_days) // in days
	{
		$this->cookie_days = $cookie_days;
	}	
	
	function deleteCookieVars()
	{
		$c = $this->getSystemID()."_";
		$l = strlen($c);
		foreach($_COOKIE as $key => $v)
		{
			if(substr($key,0,$l) == $c)
			{
				echo substr($key,$l);
				$this->setCookieVar(substr($key,$l), "");
			}
		}
	}

	function setCookieVar($key, $value)
	{
		if($value == "")
		{
			setcookie($this->getSystemID()."_".$key, "", time() -1 , "/" );
		}else
			setcookie($this->getSystemID()."_".$key, $value, time() + (3600 * 24 * $this->cookie_days), "/" );
		
	}
	
	function getCookieVar($key, $default = '')
	{
		if (isset ($_COOKIE[$this->getSystemID()."_".$key]))
			return $_COOKIE[$this->getSystemID()."_".$key];
	
		return $default;
	}
	
	
	
}


