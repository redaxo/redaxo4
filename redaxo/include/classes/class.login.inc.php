<?

// class login 1.0
// 
// erstellt 01.12.2003
// pergopa kristinus gbr
// lange strasse 31
// 60311 Frankfurt/M.
// www.pergopa.de
// ersteller: j.kristinus

class login{

	var $DB;
	var $error_page;
	var $session_duration;
	var $login_query;
	var $user_query;
	var $system_id;
	var $usr_login;
	var $usr_psw;
	var $logout;
	var $message;
	var $uid;
	var $USER;
	var $text;
	
	function login()
	{
		$this->DB = 1;
		$this->logout = false;
		$this->message = "";
		$this->system_id = "default";
		$this->setLanguage();
	}
	
	function setLanguage($lang = "en")
	{
		if ($lang == "de")
		{
			$this->text[10] = "Session beendet.";
			$this->text[20] = "ID nicht gefunden.";
			$this->text[30] = "Fehler beim Login.";
			$this->text[40] = "Bitte einloggen.";
			$this->text[50] = "Ausgeloggt.";
		}else
		{
			$this->text[10] = "your session is expired !";
			$this->text[20] = "uid not found";
			$this->text[30] = "login wrong";
			$this->text[40] = "login please";
			$this->text[50] = "You logged out.";
		}		
	}
	
	function setSqlDb($DB)
	{
		$this->DB = $DB;
	}
	
	function setSysID($system_id)
	{
		$this->system_id = $system_id;
	}
	
	function setSessiontime($session_duration)
	{
		$this->session_duration = $session_duration;	
	}
	
	function setLogin($usr_login,$usr_psw)
	{
		$this->usr_login = $usr_login;
		$this->usr_psw = $usr_psw;
	}
	
	function setLogout($logout)
	{
		$this->logout = $logout;	
	}
	
	function setUserquery($login_query)
	{
		$this->user_query = $login_query;
	}	
	
	function setLoginquery($user_query)
	{
		$this->login_query = $user_query;
	}
	
	function setUserID($uid)
	{
		$this->uid = $uid;
	}
	
	function setMessage($message)
	{
		$this->message = $message;	
	}
	
	function checkLogin()
	{
		
		// wenn logout dann header schreiben und auf error seite verweisen
		// message schreiben
		
		$ok = false;
		
		if (!$this->logout)
		{
			
			if ($this->usr_login != "")
			{
				// wenn login daten eingegeben dann checken
				// auf error seite verweisen und message schreiben
				
				$this->USER = new sql($this->DB);
				$USR_LOGIN = $this->usr_login;
				$USR_PSW = $this->usr_psw;

				$query = str_replace("USR_LOGIN",$this->usr_login,$this->login_query);
				$query = str_replace("USR_PSW",$this->usr_psw,$query);
				
//				 $this->USER->debugsql = 1;
				
				$this->USER->setQuery($query);
				if ($this->USER->getRows() == 1)
				{
					$ok = true;
					$_SESSION[UID][$this->system_id] = $this->USER->getValue($this->uid);
                    
                    // Erfolgreicher Login in der DB vermerken
                    $sql = new sql();
                    $sql->setQuery( 'UPDATE rex_user SET lasttrydate ="'. time() .'" WHERE login ="'. $this->usr_login .'"');
				}else
				{
					$this->message = $this->text[30];
					$_SESSION[UID][$this->system_id] = "";
					// session_unregister("REX_SESSION");
				}
								
			}elseif($_SESSION[UID][$this->system_id]!="")
			{
				// wenn kein login und kein logout dann nach sessiontime checken
				// message schreiben und falls falsch auf error verweisen
								
				$this->USER = new sql($this->DB);
				$query = str_replace("USR_UID",$_SESSION[UID][$this->system_id],$this->user_query);
				
				// $this->USER->debugsql = 1;
				
				$this->USER->setQuery($query);
				if ($this->USER->getRows() == 1)
				{
					
					// echo $REX_SESSION[ST][$this->system_id]." + ".$this->session_duration." > ".time();
					
					if (($_SESSION[ST][$this->system_id]+$this->session_duration)>time())
					{
						$ok = true;
						$_SESSION[UID][$this->system_id] = $this->USER->getValue($this->uid);
					}else
					{
						$this->message = $this->text[10];	
					}					
				}else
				{
					$this->message = $this->text[20];
				}
			}else
			{
				$this->message = $this->text[40];
				$ok = false;
			}
		}else
		{
			$this->message = $this->text[50];
			$_SESSION[UID][$this->system_id] = "";
			// session_unregister("REX_SESSION");
		}
		
		if ($ok)
		{
			// wenn alles ok dann REX[UID][system_id) schreiben
			$_SESSION[ST][$this->system_id] = time();
			
			//session_register("REX_SESSION");

			// $_SESSION['REX_SESSION'] = $REX_SESSION;

		}else
		{
			// wenn nicht, dann UID loeschen und error seite
			
			$_SESSION[ST][$this->system_id] = "";
			$_SESSION[UID][$this->system_id] = "";
			
			// header("Location: $this->error_page"."&FORM[loginmessage]=".urlencode($this->message));
			// echo $this->message;
			
		}
		
		return $ok;				
	}
	
	function getValue($value)
	{
		return $this->USER->getValue($value);
	}
}

?>