<?php

/**
 * Importiert den SQL Dump $filename in die Datenbank
 * 
 * @param string Pfad + Dateinamen zur SQL-Datei
 *  
 * @return array Gibt ein Assoc. Array zurück.
 *               'state' => boolean (Status ob fehler aufgetreten sind)
 *               'message' => Evtl. Status/Fehlermeldung  
 */
function rex_a1_import_db($filename,$replace_rex = false)
{
  global $REX, $I18N_IM_EXPORT;

  $return = array ();
  $return['state'] = false;
  $return['message'] = '';
  
  $msg = '';
  $error = '';

  if ($filename == '')
  {
    $return['message'] = $I18N_IM_EXPORT->msg('no_import_file_chosen_or_wrong_version').'<br>';
    return $return;
  }

  $h = fopen($filename, "r");
  $conts = fread($h, filesize($filename));
  fclose($h);

  // Versionsstempel prüfen
  // ## Redaxo Database Dump Version x.x
  $rex_version = strpos($conts, "## Redaxo Database Dump Version ".$REX['VERSION']);
  if($rex_version === FALSE)
  {
    $return['message'] = $I18N_IM_EXPORT->msg("no_valid_import_file").". [## Redaxo Database Dump Version ".$REX['VERSION']."] is missing";
    return $return;
  }
  else
  {
    // Versionsstempel entfernen
    $conts = trim(str_replace("## Redaxo Database Dump Version ".$REX['VERSION'], "", $conts));
  }

  // Prefix prüfen
  // ## Prefix rex_
  $rex_prefix = strpos($conts, "## Prefix ". $REX['TABLE_PREFIX']);
  if($replace_rex)
  {
  	$conts = trim(str_replace("## Prefix rex_", "", $conts));
  	
  	$conts = str_replace("TABLE rex_","TABLE ".$REX['TABLE_PREFIX'],$conts);
  	$conts = str_replace("INTO rex_","INTO ".$REX['TABLE_PREFIX'],$conts);
  	$conts = str_replace("EXISTS rex_","EXISTS ".$REX['TABLE_PREFIX'],$conts);
  	
  }elseif($rex_prefix === FALSE)
  {
    $return['message'] = $I18N_IM_EXPORT->msg("no_valid_import_file").". [## Prefix ". $REX['TABLE_PREFIX'] ."] does not match config in master.inc.php";
    return $return;
  }
  else
  {
    // Prefix entfernen
    $conts = trim(str_replace("## Prefix ". $REX['TABLE_PREFIX'], "", $conts));
  }
  
  // Ordner /generated komplett leeren
  rex_deleteDir($REX['INCLUDE_PATH'].'/generated/articles');
  rex_deleteDir($REX['INCLUDE_PATH'].'/generated/files');
  rex_deleteDir($REX['INCLUDE_PATH'].'/generated/templates');
  
  // Datei aufteilen
  $lines = explode("\n", $conts);

  $add = new sql;
  // $add->debugsql = 1;
  foreach ($lines as $line)
  {
  	$line = trim($line,"\r"); // Windows spezifische extras
  	$line = trim($line, ";"); // mysql 3.x 
    $add->setquery($line);
    $add->flush();
  }

  $msg .= $I18N_IM_EXPORT->msg("database_imported").". ".$I18N_IM_EXPORT->msg("entry_count", count($lines))."<br>";

  // CLANG Array aktualisieren
  unset ($REX['CLANG']);
  $db = new sql;
  $db->setQuery("select * from ". $REX['TABLE_PREFIX'] ."clang");
  for ($i = 0; $i < $db->getRows(); $i++)
  {
    $id = $db->getValue("id");
    $name = $db->getValue("name");
    $REX['CLANG'][$id] = $name;
    $db->next();
  }

  // prüfen, ob eine user tabelle angelegt wurde
  $result = $db->get_array('SHOW TABLES');
  $user_table_found = false;
  foreach ($result as $row)
  {
    if (in_array($REX['TABLE_PREFIX'].'user', $row))
    {
      $user_table_found = true;
      break;
    }
  }

  if (!$user_table_found)
  {
    $create_user_table = '
    CREATE TABLE '. $REX['TABLE_PREFIX'] .'user
     ( 
       user_id int(11) NOT NULL auto_increment,
       name varchar(255) NOT NULL,
       description text NOT NULL,
       login varchar(50) NOT NULL,
       psw varchar(50) NOT NULL,
       status varchar(5) NOT NULL,
       rights text NOT NULL,
       login_tries tinyint(4) NOT NULL DEFAULT 0,
       createuser varchar(255) NOT NULL,
       updateuser varchar(255) NOT NULL,
       createdate int(11) NOT NULL DEFAULT 0,
       updatedate int(11) NOT NULL DEFAULT 0,
       lasttrydate int(11) NOT NULL DEFAULT 0,
       session_id varchar(255) NOT NULL,
       PRIMARY KEY(user_id)
     ) TYPE=MyISAM;';
    $db->setQuery($create_user_table);
    $error = $db->getError();
    if($error != '')
    {
      // evtl vorhergehende meldungen löschen, damit nur der fehler angezeigt wird
      $msg = '';
      $msg .= $error;
    }
  }

  // generated neu erstellen, wenn kein Fehler aufgetreten ist
  if($error == '')
  {
    $msg .= rex_generateAll();
    $return['state'] = true;
  }
  
  $return['message'] = $msg;
  
  return $return;
}

/**
 * Importiert das Tar-Archiv $filename in den Ordner /files
 * 
 * @param string Pfad + Dateinamen zum Tar-Archiv
 * 
 * @return array Gibt ein Assoc. Array zurück.
 *               'state' => boolean (Status ob fehler aufgetreten sind)
 *               'message' => Evtl. Status/Fehlermeldung  
 */
function rex_a1_import_files($filename)
{
  global $REX, $I18N_IM_EXPORT;

  $return = array ();
  $return['state'] = false;

  if ($filename == '')
  {
    $return['message'] = $I18N_IM_EXPORT->msg("no_import_file_chosen")."<br>";
    return $return;
  }

  // Ordner /files komplett leeren
  rex_deleteDir($REX['INCLUDE_PATH']."/../../files");

  $tar = new tar;
  $tar->openTAR($filename);
  if (!$tar->extractTar())
  {
    $msg = $I18N_IM_EXPORT->msg("problem_when_extracting")."<br>";
    if (count($tar->message) > 0)
    {
      $msg .= $I18N_IM_EXPORT->msg("create_dirs_manually")."<br>";
      reset($tar->message);
      for ($fol = 0; $fol < count($tar->message); $fol++)
      {
        $msg .= rex_absPath(str_replace("'", "", key($tar->message)))."<br>";
        next($tar->message);
      }
    }
  }
  else
  {
    $msg = $I18N_IM_EXPORT->msg("file_imported")."<br>";
  }

  $return['state'] = true;
  $return['message'] = $msg;
  return $return;
}

/**
 * Erstellt einen SQL Dump, der die aktuellen Datebankstruktur darstellt
 * @return string SQL Dump der Datenbank
 */
function rex_a1_export_db()
{
  global $REX;

  $tabs = new sql;
  $tabs->setquery("SHOW TABLES");
  $dump = '';

  for ($i = 0; $i < $tabs->rows; $i++, $tabs->next())
  {
    $tab = $tabs->getValue("Tables_in_".$REX['DB']['1']['NAME']);
    if (strstr($tab, $REX['TABLE_PREFIX']) == $tab && $tab != $REX['TABLE_PREFIX'].'user')
    {
      $cols = new sql;
      $cols->setquery("SHOW COLUMNS FROM ".$tab);
      $query = "DROP TABLE IF EXISTS ".$tab.";\nCREATE TABLE ".$tab." (";
      $key = array ();

      // Spalten auswerten
      for ($j = 0; $j < $cols->rows; $j++, $cols->next())
      {
        $colname = $cols->getValue("Field");
        $coltype = $cols->getValue("Type");

        // Null Werte
        if ($cols->getValue("Null") == 'YES')
        {
          $colnull = "NULL";
        }
        else
        {
          $colnull = "NOT NULL";
        }

        // Default Werte
        if ($cols->getValue("Default") != '')
        {
          $coldef = "DEFAULT ".$cols->getValue("Default")." ";
        }
        else
        {
          $coldef = "";
        }

        // Spezial Werte
        $colextra = $cols->getValue("Extra");
        if ($cols->getValue("Key") != '')
        {
          $key[] = $colname;
          $colnull = "NOT NULL";
        }

        $query .= " $colname $coltype $colnull $coldef $colextra";
        if ($j +1 != $cols->rows)
        {
          $query .= ",";
        }
      }

      // Primärschlüssel Auswerten
      if (count($key) > 0)
      {
        $query .= ", PRIMARY KEY(";
        for ($k = 0, reset($key); $k < count($key); $k++, next($key))
        { // <-- yeah super for schleife, rock 'em hard :)
          $query .= current($key);
          if ($k +1 != count($key))
            $query .= ",";
        }
        $query .= ")";
      }
      $query .= ")TYPE=MyISAM;";

      $dump .= $query."\n";

      // Inhalte der Tabelle Auswerten
      $cont = new sql;
      $cont->setquery("SELECT * FROM ".$tab);
      for ($j = 0; $j < $cont->rows; $j++, $cont->next())
      {
        $query = "INSERT INTO ".$tab." VALUES (";
        $cols->counter = 0;
        for ($k = 0; $k < $cols->rows; $k++, $cols->next())
        {
          $con = $cont->getValue($cols->getValue("Field"));

          if (is_numeric($con))
          {
            $query .= "'".$con."'";
          }
          else
          {
            $query .= "'".addslashes($con)."'";
          }

          if ($k +1 != $cols->rows)
          {
            $query .= ",";
          }
        }
        $query .= ");";
        $dump .= str_replace(array (
          "\r\n",
          "\n"
        ), '\r\n', $query)."\n";
      }
    }
  }

  // Versionsstempel hinzufügen
  $dump = str_replace("\r", "", $dump);
  $header = "## Redaxo Database Dump Version ".$REX['VERSION']."\n";
  $header .= "## Prefix ". $REX['TABLE_PREFIX'] ."\n";

  return $header . $dump;
}

/**
 * Exportiert alle Ordner $folders aus dem Verzeichnis /files
 * 
 * @param array Array von Ordnernamen, die exportiert werden sollen
 * @param string Pfad + Dateiname, wo das Tar File erstellt werden soll
 * 
 * @access public
 * @return string Inhalt des Tar-Archives als String 
 */
function rex_a1_export_files($folders, $filename, $ext = '.tar.gz')
{
  global $REX;

  $tar = new tar;
  foreach ($folders as $key => $item)
  {
    _rex_a1_add_folder_to_tar($tar, $REX['INCLUDE_PATH']."/../../", $key);
  }

  $content = $tar->toTarOutput($filename.$ext, true);
  return $content;
}

/**
 * Fügt einem Tar-Archiv ein Ordner von Dateien hinzu 
 * @access protected
 */
function _rex_a1_add_folder_to_tar(& $tar, $path, $dir)
{
  $handle = opendir($path.$dir);
  $array_indx = 0;
  #$tar->addFile($path.$dir."/",TRUE);
  while (false !== ($file = readdir($handle)))
  {
    if(substr($file, 0, 4) != 'tmp_')
    {
      $dir_array[$array_indx] = $file;
      $array_indx++;
    }
  }
  foreach ($dir_array as $n)
  {
    #echo $n."<br>";
    if (($n != '.') && ($n != '..'))
    {
      #echo "hier : $n <br>";
      if (is_dir($path.$dir."/".$n))
      {
        _rex_a1_add_folder_to_tar($tar, $path.$dir."/", $n);
      }

      if (!is_dir($path.$dir."/".$n))
      {
        $tar->addFile($path.$dir."/".$n, true);
      }
      #echo $path.$dir."/".$n."<br>";
    }
  }
}
?>