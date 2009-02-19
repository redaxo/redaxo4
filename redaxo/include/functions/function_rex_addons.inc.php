<?php


/**
 * Addon Funktionen
 * @package redaxo4
 * @version $Id: function_rex_addons.inc.php,v 1.3 2008/02/25 09:51:18 kills Exp $
 */

function rex_install_addon($addons, $addonname, $installDump = true)
{
  global $REX, $I18N;
  $state = true;

  $install_dir = rex_addons_folder($addonname);
  $install_file = $install_dir.'install.inc.php';
  $install_sql = $install_dir.'install.sql';
  $config_file = $install_dir.'config.inc.php';

  // Prüfen des Addon Ornders auf Schreibrechte,
  // damit das Addon später wieder gelöscht werden kann
  $state = rex_is_writable($install_dir);

  if ($state === true)
  {
    if (is_readable($install_file))
    {
      require $install_file;

      // Wurde das "install" Flag gesetzt, oder eine Fehlermeldung ausgegeben? Wenn ja, Abbruch
      if (!OOAddon :: isInstalled($addonname) || !empty( $REX['ADDON']['installmsg'][$addonname]))
      {
        $state = $I18N->msg('addon_no_install', $addonname).'<br />';
        if ($REX['ADDON']['installmsg'][$addonname] == '')
        {
          $state .= $I18N->msg('addon_no_reason');
        }
        else
        {
          $state .= $REX['ADDON']['installmsg'][$addonname];
        }
      }
      else
      {
        // check if config file exists
        if (is_readable($config_file))
        {
          if (!OOAddon :: isActivated($addonname))
          {
            require $config_file;
          }
        }
        else
        {
          $state = $I18N->msg('addon_config_not_found');
        }

			  if($installDump === true && $state === true && is_readable($install_sql))
			  {
					$state = rex_install_dump($install_sql);

          if($state !== true)
            $state = 'Error found in install.sql:<br />'. $state;
				}

        // Installation ok
        if ($state === true)
        {
          // regenerate Addons file
          $state = rex_generateAddons($addons);
        }
      }
    }
    else
    {
      $state = $I18N->msg('addon_install_not_found');
    }
  }

  if($state !== true)
    $REX['ADDON']['install'][$addonname] = 0;

  return $state;
}

function rex_activate_addon($addons, $addonname)
{
  global $REX, $I18N;
  $state = true;

  if (OOAddon :: isInstalled($addonname))
  {
    $REX['ADDON']['status'][$addonname] = 1;
    // regenerate Addons file
    $state = rex_generateAddons($addons);
  }
  else
  {
    $state = $I18N->msg('addon_no_activation', $addonname);
  }

  if($state !== true)
    $REX['ADDON']['status'][$addonname] = 0;

  return $state;
}

function rex_deactivate_addon($addons, $addonname)
{
  global $REX;
  $state = true;

  $REX['ADDON']['status'][$addonname] = 0;

  // regenerate Addons file
  $state = rex_generateAddons($addons);

  return $state;
}

function rex_uninstall_addon($addons, $addonname)
{
  global $REX, $I18N;

  $state = true;
  $install_dir = rex_addons_folder($addonname);
  $uninstall_file = $install_dir.'uninstall.inc.php';
  $uninstall_sql = $install_dir.'uninstall.sql';

  if (is_readable($uninstall_file))
  {
    require $uninstall_file;

    // Wurde das "uninstall" Flag gesetzt, oder eine Fehlermeldung ausgegeben? Wenn ja, Abbruch
    if (OOAddon :: isInstalled($addonname) || !empty($REX['ADDON']['installmsg'][$addonname]))
    {
      $state = $I18N->msg('addon_no_uninstall', $addonname).'<br/>';
      if (empty( $REX['ADDON']['installmsg'][$addonname]))
      {
        $state .= $I18N->msg('addon_no_reason');
      }
      else
      {
        $state .= $REX['ADDON']['installmsg'][$addonname];
      }
    }
    else
    {
      $state = rex_deactivate_addon($addons, $addonname);

		  if($state === true && is_readable($uninstall_sql))
		  {
				$state = rex_install_dump($uninstall_sql);

        if($state !== true)
          $state = 'Error found in uninstall.sql:<br />'. $state;
			}

      if ($state === true)
      {
        // regenerate Addons file
        $state = rex_generateAddons($addons);
      }
    }
  }
  else
  {
    $state = $I18N->msg("addon_uninstall_not_found");
  }

  // Fehler beim uninstall -> Addon bleibt installiert
  if($state !== true)
    $REX['ADDON']['install'][$addonname] = 1;

  return $state;
}

function rex_delete_addon($addons, $addonname)
{
  global $REX, $I18N;

  // System AddOns dürfen nicht gelöscht werden!
  if(in_array($addonname, $REX['SYSTEM_ADDONS']))
    return $I18N->msg('addon_systemaddon_delete_not_allowed');

  // zuerst deinstallieren
  $state = rex_uninstall_addon($addons, $addonname, false);

  if ($state === true)
  {
    // bei erfolg, komplett löschen
    rex_deleteDir($REX['INCLUDE_PATH']."/addons/$addonname", true);
    // regenerate Addons file
    $state = rex_generateAddons($addons);
  }

  return $state;
}

function rex_addons_folder($addon = null)
{
  global $REX;
  
  if(!is_null($addon))
    return $REX['INCLUDE_PATH'] .DIRECTORY_SEPARATOR. 'addons' .DIRECTORY_SEPARATOR. $addon .DIRECTORY_SEPARATOR;
  
  return $REX['INCLUDE_PATH']. DIRECTORY_SEPARATOR. 'addons' .DIRECTORY_SEPARATOR;
}

function rex_read_addons_folder($folder = '')
{
  if ($folder == '')
  {
    $folder = rex_addons_folder();
  }

  $addons = array ();
  $hdl = opendir($folder);
  while (($file = readdir($hdl)) !== false)
  {
    if ($file != '.' && $file != '..' && is_dir($folder.$file.'/.'))
    {
      $addons[] = $file;
    }
  }
  closedir($hdl);

  // Sortiere Array
  natsort($addons);

  return $addons;
}

// ------------------------------------- Helpers

/**
 * Importiert die gegebene SQL-Datei in die Datenbank
 *
 * @return true bei Erfolg, sonst eine Fehlermeldung
 */
function rex_install_dump($file, $debug = false)
{
  $sql = new rex_sql();
  $sql->debugsql = $debug;
  $error = '';

  foreach (rex_read_sql_dump($file) as $query)
  {
    $sql->setQuery(rex_install_prepare_query($query));

    if (($sqlerr = $sql->getError()) != '')
    {
      $error .= $sqlerr."\n<br />";
    }
  }

  return $error == '' ? true : $error;
}

function rex_install_prepare_query($qry)
{
  global $REX, $REX_USER;

  // REX_USER gibts im Setup nicht
  if(isset($REX_USER))
    $qry = str_replace('%USER%', $REX_USER->getValue('login'), $qry);

  $qry = str_replace('%TIME%', time(), $qry);
  $qry = str_replace('%TABLE_PREFIX%', $REX['TABLE_PREFIX'], $qry);
  $qry = str_replace('%TEMP_PREFIX%', $REX['TEMP_PREFIX'], $qry);

  return $qry;
}

/**
 * Removes comment lines and splits up large sql files into individual queries
 *
 * Last revision: September 23, 2001 - gandon
 *
 * @param   array    the splitted sql commands
 * @param   string   the sql commands
 * @param   integer  the MySQL release number (because certains php3 versions
 *                   can't get the value of a constant from within a function)
 *
 * @return  boolean  always true
 *
 * @access  public
 */
// Taken from phpmyadmin (read_dump.lib.php: PMA_splitSqlFile)
function PMA_splitSqlFile(& $ret, $sql, $release)
{
  // do not trim, see bug #1030644
  //$sql          = trim($sql);
  $sql = rtrim($sql, "\n\r");
  $sql_len = strlen($sql);
  $char = '';
  $string_start = '';
  $in_string = FALSE;
  $nothing = TRUE;
  $time0 = time();

  for ($i = 0; $i < $sql_len; ++ $i)
  {
    $char = $sql[$i];

    // We are in a string, check for not escaped end of strings except for
    // backquotes that can't be escaped
    if ($in_string)
    {
      for (;;)
      {
        $i = strpos($sql, $string_start, $i);
        // No end of string found -> add the current substring to the
        // returned array
        if (!$i)
        {
          $ret[] = $sql;
          return TRUE;
        }
        // Backquotes or no backslashes before quotes: it's indeed the
        // end of the string -> exit the loop
        else
          if ($string_start == '`' || $sql[$i -1] != '\\')
          {
            $string_start = '';
            $in_string = FALSE;
            break;
          }
        // one or more Backslashes before the presumed end of string...
        else
        {
          // ... first checks for escaped backslashes
          $j = 2;
          $escaped_backslash = FALSE;
          while ($i - $j > 0 && $sql[$i - $j] == '\\')
          {
            $escaped_backslash = !$escaped_backslash;
            $j ++;
          }
          // ... if escaped backslashes: it's really the end of the
          // string -> exit the loop
          if ($escaped_backslash)
          {
            $string_start = '';
            $in_string = FALSE;
            break;
          }
          // ... else loop
          else
          {
            $i ++;
          }
        } // end if...elseif...else
      } // end for
    } // end if (in string)

    // lets skip comments (/*, -- and #)
    else
      if (($char == '-' && $sql_len > $i +2 && $sql[$i +1] == '-' && $sql[$i +2] <= ' ') || $char == '#' || ($char == '/' && $sql_len > $i +1 && $sql[$i +1] == '*'))
      {
        $i = strpos($sql, $char == '/' ? '*/' : "\n", $i);
        // didn't we hit end of string?
        if ($i === FALSE)
        {
          break;
        }
        if ($char == '/')
          $i ++;
      }

    // We are not in a string, first check for delimiter...
    else
      if ($char == ';')
      {
        // if delimiter found, add the parsed part to the returned array
        $ret[] = array ('query' => substr($sql, 0, $i), 'empty' => $nothing);
        $nothing = TRUE;
        $sql = ltrim(substr($sql, min($i +1, $sql_len)));
        $sql_len = strlen($sql);
        if ($sql_len)
        {
          $i = -1;
        }
        else
        {
          // The submited statement(s) end(s) here
          return TRUE;
        }
      } // end else if (is delimiter)

    // ... then check for start of a string,...
    else
      if (($char == '"') || ($char == '\'') || ($char == '`'))
      {
        $in_string = TRUE;
        $nothing = FALSE;
        $string_start = $char;
      } // end else if (is start of string)

    elseif ($nothing)
    {
      $nothing = FALSE;
    }

    // loic1: send a fake header each 30 sec. to bypass browser timeout
    $time1 = time();
    if ($time1 >= $time0 +30)
    {
      $time0 = $time1;
      header('X-pmaPing: Pong');
    } // end if
  } // end for

  // add any rest to the returned array
  if (!empty ($sql) && preg_match('@[^[:space:]]+@', $sql))
  {
    $ret[] = array ('query' => $sql, 'empty' => $nothing);
  }

  return TRUE;
} // end of the 'PMA_splitSqlFile()' function

/**
 * Reads a file and split all statements in it.
 *
 * @param $file String Path to the SQL-dump-file
 */
function rex_read_sql_dump($file)
{
  if (is_file($file) && is_readable($file))
  {
    $ret = array ();
    $sqlsplit = '';
    $fileContent = file_get_contents($file);
    PMA_splitSqlFile($sqlsplit, $fileContent, '');

    if (is_array($sqlsplit))
    {
      foreach ($sqlsplit as $qry)
      {
        $ret[] = $qry['query'];
      }
    }

    return $ret;
  }

  return false;
}

/**
 * Sucht innerhalb des $REX['ADDON']['page'] Array rekursiv nach der page
 * $needle
 *
 * Gibt bei erfolgreicher Suche den Namen des Addons zurück, indem die page
 * gefuden wurde, sonst false
 */
function rex_search_addon_page($needle, $haystack = null)
{
  global $REX;

  if($haystack === null)
    $haystack = $REX['ADDON']['page'];

  foreach($haystack as $key => $value)
  {
    if(is_array($value))
    {
      $found = rex_search_addon_page($needle, $value);
    }
    else
    {
      $found = $needle == $value;
    }

    if($found !== false)
      return $key;
  }

  return false;
}