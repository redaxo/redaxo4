<?php


/**
 * Klasse zum handling des Login/Logout-Mechanismuses
 *
 * @package redaxo3
 * @version $Id$
 */

class rex_login_sql extends rex_sql
{
  function isValueOf($feld, $prop)
  {
    if ($prop == '')
    {
      return true;
    }
    else
    {
      if ($feld == 'rights')
        return strpos($this->getValue($feld), '#' . $prop . '#') !== false;
      else
        return strpos($this->getValue($feld), $prop) !== false;
    }
  }

  function hasPerm($perm)
  {
    return $this->isValueOf('rights', $perm);
  }
}

class rex_login
{
  var $DB;
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
  var $passwordfunction;
  var $cache;
  var $login_status;

  function rex_login()
  {
    $this->DB = 1;
    $this->logout = false;
    $this->message = "";
    $this->system_id = "default";
    $this->cache = false;
    $this->login_status = 0; // 0 = nochchecken, 1 = ok, -1 = notok
    session_start();
  }

  /**
   * Setzt, ob die Ergebnisse der Login-Abfrage
   * pro Seitenaufruf gecached werden sollen
   */
  function setCache($status = true)
  {
    $this->cache = $status;
  }

  /**
   * Setzt die Id der zu verwendenden SQL Connection
   */
  function setSqlDb($DB)
  {
    $this->DB = $DB;
  }

  /**
   * Setzt eine eindeutige System Id, damit mehrere
   * Sessions auf der gleichen Domain unterschieden werden können
   */
  function setSysID($system_id)
  {
    $this->system_id = $system_id;
  }

  /**
   * Setzt das Session Timeout
   */
  function setSessiontime($session_duration)
  {
    $this->session_duration = $session_duration;
  }

  /**
   * Setzt den Login und das Password
   */
  function setLogin($usr_login, $usr_psw)
  {
    $this->usr_login = $usr_login;
    $this->usr_psw = $this->encryptPassword($usr_psw);
  }

  /**
   * Markiert die aktuelle Session als ausgeloggt
   */
  function setLogout($logout)
  {
    $this->logout = $logout;
  }

  /**
   * Setzt den UserQuery
   *
   * Dieser wird benutzt, um einen bereits eingeloggten User
   * im Verlauf seines Aufenthaltes auf der Webseite zu verifizieren
   */
  function setUserquery($login_query)
  {
    $this->user_query = $login_query;
  }

  /**
   * Setzt den LoginQuery
   *
   * Dieser wird benutzt, um den eigentlichne Loginvorgang durchzuführen.
   * Hier wird das eingegebene Password und der Login eingesetzt.
   */
  function setLoginquery($user_query)
  {
    $this->login_query = $user_query;
  }

  /**
   * Setzt den Namen der Spalte, der die User-Id enthält
   */
  function setUserID($uid)
  {
    $this->uid = $uid;
  }

  /**
   * Setzt einen Meldungstext
   */
  function setMessage($message)
  {
    $this->message = $message;
  }

  /**
   * Prüft die mit setLogin() und setPassword() gesetzten Werte
   * anhand des LoginQueries/UserQueries und gibt den Status zurück
   *
   * Gibt true zurück bei erfolg, sonst false
   */
  function checkLogin()
  {
    global $REX, $I18N;
    // wenn logout dann header schreiben und auf error seite verweisen
    // message schreiben

    $ok = false;

    if (!$this->logout)
    {

      // checkLogin schonmal ausgeführt ? gecachte ausgabe erlaubt ?
      if ($this->cache && $this->login_status > 0)
        return true;
      elseif ($this->cache && $this->login_status < 0) return false;

      if ($this->usr_login != "")
      {
        // wenn login daten eingegeben dann checken
        // auf error seite verweisen und message schreiben

        $this->USER = new rex_login_sql($this->DB);
        $USR_LOGIN = $this->usr_login;
        $USR_PSW = $this->usr_psw;

        $query = str_replace("USR_LOGIN", $this->usr_login, $this->login_query);
        $query = str_replace("USR_PSW", $this->usr_psw, $query);

        $this->USER->setQuery($query);
        if ($this->USER->getRows() == 1)
        {
          $ok = true;
          $this->setSessionVar('UID', $this->USER->getValue($this->uid));
          $this->sessionFixation();
        }
        else
        {
          $this->message = $I18N->msg('login_error', $REX['RELOGINDELAY']);
          $this->setSessionVar('UID', '');
        }

      }
      elseif ($this->getSessionVar('UID') != '')
      {
        // wenn kein login und kein logout dann nach sessiontime checken
        // message schreiben und falls falsch auf error verweisen

        $this->USER = new rex_login_sql($this->DB);
        $query = str_replace("USR_UID", $this->getSessionVar('UID'), $this->user_query);

        $this->USER->setQuery($query);
        if ($this->USER->getRows() == 1)
        {
          if (($this->getSessionVar('STAMP') + $this->session_duration) > time())
          {
            $ok = true;
            $this->setSessionVar('UID', $this->USER->getValue($this->uid));
          }
          else
          {
	          $this->message = $I18N->msg('login_session_expired');
          }
        }
        else
        {
          $this->message = $I18N->msg('login_user_not_found');
        }
      }
      else
      {
        $this->message = $I18N->msg('login_welcome');
        $ok = false;
      }
    }
    else
    {
      $this->message = $I18N->msg('login_logged_out');
      $this->setSessionVar('UID', '');
    }

    if ($ok)
    {
      // wenn alles ok dann REX[UID][system_id) schreiben
      $this->setSessionVar('STAMP', time());
    }
    else
    {
      // wenn nicht, dann UID loeschen und error seite
      $this->setSessionVar('STAMP', '');
      $this->setSessionVar('UID', '');
    }

    if ($ok)
      $this->login_status = 1;
    else
      $this->login_status = -1;

    return $ok;
  }

  /**
   * Gibt einen Benutzer-Spezifischen Wert zurück
   */
  function getValue($value)
  {
    return $this->USER->getValue($value);
  }

  /**
   * Setzt eine Password-Funktion
   */
  function setPasswordFunction($pswfunc)
  {
    $this->passwordfunction = $pswfunc;
  }

  /**
   * Verschlüsselt den übergebnen String, falls eine Password-Funktion gesetzt ist.
   */
  function encryptPassword($psw)
  {
    if ($this->passwordfunction == "")
      return $psw;

    return call_user_func($this->passwordfunction, $psw);
  }

  /**
   * Setzte eine Session-Variable
   */
  function setSessionVar($varname, $value)
  {
    $_SESSION[$this->system_id][$varname] = $value;
  }

  /**
   * Gibt den Wert einer Session-Variable zurück
   */
  function getSessionVar($varname, $default = '')
  {
    if (isset ($_SESSION[$this->system_id][$varname]))
      return $_SESSION[$this->system_id][$varname];

    return $default;
  }

  /*
   * Session fixation
   *
   */
  function sessionFixation()
  {
    $tmp = $_SESSION;
    session_unset();
    session_regenerate_id(true);
    $_SESSION = $tmp;
  }
}

class rex_backend_login extends rex_login
{
  function rex_backend_login()
  {
    global $REX;

    parent::rex_login();

    $this->setSqlDb(1);
    $this->setSysID($REX['INSTNAME']);
    $this->setSessiontime(3000);
    $this->setUserID($REX['TABLE_PREFIX'].'user.user_id');
    $this->setUserquery('SELECT * FROM '.$REX['TABLE_PREFIX'].'user WHERE status=1 AND user_id = "USR_UID"');
    $this->setLoginquery('SELECT * FROM '.$REX['TABLE_PREFIX'].'user WHERE status=1 AND login = "USR_LOGIN" AND psw = "USR_PSW" AND lasttrydate <'. (time()-$REX['RELOGINDELAY']).' AND login_tries<'.$REX['MAXLOGINS']);
  }

  function checkLogin()
  {
    global $REX;

    $fvs = new rex_sql;
    // $fvs->debugsql = true;
    $check = parent::checkLogin();

    if($check)
    {
      // gelungenen versuch speichern | login_tries = 0
      if($this->usr_login != '')
      {
        $this->sessionFixation();
        $fvs->setQuery('UPDATE '.$REX['TABLE_PREFIX'].'user SET login_tries=0, lasttrydate='.time().', session_id="'. session_id() .'" WHERE login="'. $this->usr_login .'"');
      }
    }
    else
    {
      // fehlversuch speichern | login_tries++
      if($this->usr_login != '')
      {
        $fvs->setQuery('UPDATE '.$REX['TABLE_PREFIX'].'user SET login_tries=login_tries+1,lasttrydate='.time().' WHERE login="'. $this->usr_login .'"');
      }
    }

    return $check;
  }
}
?>