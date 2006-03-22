<?php
/** 
 *  
 * @package redaxo3
 * @version $Id$
 */ 

// class login 1.0
// 
// erstellt 01.12.2003
// pergopa kristinus gbr
// lange strasse 31
// 60311 Frankfurt/M.
// www.pergopa.de
// ersteller: j.kristinus

class rex_login_sql extends sql{

  function isValueOf($feld, $prop)
  {
    if ($prop == "")
    {
      return TRUE;
    }
    else 
    {
      if ($feld == "rights") return strpos($this->getValue( $feld), "#".$prop."#") !== false;
      else return strpos($this->getValue( $feld), $prop) !== false;
    }
  }
  
  function hasPerm($perm)
  {
    return $this->isValueOf("rights",$perm);
  }
}

class rex_login{

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
  var $passwordfunction;
  
  function rex_login()
  {
    $this->DB = 1;
    $this->logout = false;
    $this->message = "";
    $this->system_id = "default";
    $this->cryptFunction = false;
    $this->setLanguage();
    
  }
  
  function setLanguage($lang = "en")
  {
  	global $REX;
    if ($lang == "de")
    {
      $this->text[10] = "Session beendet.";
      $this->text[20] = "ID nicht gefunden.";
      $this->text[30] = "Fehler beim Login. Bitte noch ".$REX['RELOGINDELAY']." Sekunden vor dem nächsten Version warten.";
      $this->text[40] = "Bitte einloggen.";
      $this->text[50] = "Ausgeloggt.";
    }else
    {
      $this->text[10] = "your session is expired !";
      $this->text[20] = "uid not found";
      $this->text[30] = "login wrong. please wait ".$REX['RELOGINDELAY']." seconds before you try again.";
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
    $this->usr_psw = $this->encryptPassword($usr_psw);
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
    global $REX;
    // wenn logout dann header schreiben und auf error seite verweisen
    // message schreiben
    
    $ok = false;
    
    if (!$this->logout)
    {
      
      if ($this->usr_login != "")
      {
        // wenn login daten eingegeben dann checken
        // auf error seite verweisen und message schreiben
        
        $this->USER = new rex_login_sql($this->DB);
        $USR_LOGIN = $this->usr_login;
        $USR_PSW = $this->usr_psw;

        $query = str_replace("USR_LOGIN",$this->usr_login,$this->login_query);
        $query = str_replace("USR_PSW",$this->usr_psw,$query);
        
        $this->USER->setQuery($query);
        if ($this->USER->getRows() == 1)
        {
          $ok = true;
          $_SESSION['UID'][$this->system_id] = $this->USER->getValue($this->uid);
                    
        }else
        {
          $this->message = $this->text[30];
          $_SESSION['UID'][$this->system_id] = "";
          // session_unregister("REX_SESSION");
        }
                
      } elseif (isset($_SESSION['UID']) && isset($_SESSION['UID'][$this->system_id]) && $_SESSION['UID'][$this->system_id] != '')
      {
        // wenn kein login und kein logout dann nach sessiontime checken
        // message schreiben und falls falsch auf error verweisen
                
        $this->USER = new rex_login_sql($this->DB);
        $query = str_replace("USR_UID",$_SESSION['UID'][$this->system_id],$this->user_query);
        
        $this->USER->setQuery($query);
        if ($this->USER->getRows() == 1)
        {
          if (($_SESSION['ST'][$this->system_id]+$this->session_duration)>time())
          {
            $ok = true;
            $_SESSION['UID'][$this->system_id] = $this->USER->getValue($this->uid);
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
      $_SESSION['UID'][$this->system_id] = "";
      // session_unregister("REX_SESSION");
    }
    
    if ($ok)
    {
      // wenn alles ok dann REX[UID][system_id) schreiben
      $_SESSION['ST'][$this->system_id] = time();

    }else
    {
      // wenn nicht, dann UID loeschen und error seite
      $_SESSION['ST'][$this->system_id] = "";
      $_SESSION['UID'][$this->system_id] = "";
    }
    return $ok;       
  }
  
  function getValue($value)
  {
    return $this->USER->getValue($value);
  }

  function setPasswordFunction($pswfunc)
  {
  	$this->passwordfunction = $pswfunc;
  }

  function encryptPassword($psw)
  {
  	if ($this->passwordfunction == "") return $psw;
  	return call_user_func($this->passwordfunction,$psw);
  }
  
}

?>