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
function rex_a1_import_db($filename)
{
  global $REX, $I18N_IM_EXPORT;

  $return = array ();
  $return['state'] = false;

  if ($filename == '')
  {
    $return['message'] = $I18N_IM_EXPORT->msg("no_import_file_chosen_or_wrong_version")."<br>";
    return $return;
  }

  $h = fopen($filename, "r");
  $conts = fread($h, filesize($filename));
  fclose($h);

  // Versionsstempel prüfen
  // ## Redaxo Database Dump Version x.x
  if (ereg("## Redaxo Database Dump Version ".$REX['version']."\n", $conts))
  {
    $return['message'] = $I18N_IM_EXPORT->msg("no_valid_import_file").". [## Redaxo Database Dump Version ".$REX['version']."] is missing<br>";
    return $return;
  }
  else
  {
    $conts = str_replace("## Redaxo Database Dump Version ".$REX['version']." \n", "", $conts);
    $lines = explode("\n", $conts);

    $add = new sql;
    foreach ($lines as $line)
    {
      $add->setquery(trim($line, ";"));
      $add->flush();
    }

    $msg = $I18N_IM_EXPORT->msg("database_imported").". ".$I18N_IM_EXPORT->msg("entry_count", count($lines))."<br>";

    // CLANG Array aktualisieren
    unset ($REX['CLANG']);
    $gl = new sql;
    $gl->setQuery("select * from rex_clang");
    for ($i = 0; $i < $gl->getRows(); $i++)
    {
      $id = $gl->getValue("id");
      $name = $gl->getValue("name");
      $REX['CLANG'][$id] = $name;
      $gl->next();
    }
    $msg .= rex_generateAll();

    $return['state'] = true;
    $return['message'] = $msg;
    return $return;
  }
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
  global $REX, $DB;

  $tabs = new sql;
  $tabs->setquery("SHOW TABLES");
  $dump = '';

  for ($i = 0; $i < $tabs->rows; $i++, $tabs->next())
  {
    $tab = $tabs->getvalue("Tables_in_".$DB[1]['NAME']);
    if (strstr($tab, $REX['TABLE_PREFIX']) == $tab && $tab != $REX['TABLE_PREFIX'].'user')
    {
      $cols = new sql;
      $cols->setquery("SHOW COLUMNS FROM ".$tab);
      $query = "DROP TABLE IF EXISTS ".$tab.";\nCREATE TABLE ".$tab." (";
      $key = array ();

      // Spalten auswerten
      for ($j = 0; $j < $cols->rows; $j++, $cols->next())
      {
        $colname = $cols->getvalue("Field");
        $coltype = $cols->getvalue("Type");

        // Null Werte
        if ($cols->getvalue("Null") == 'YES')
        {
          $colnull = "NULL";
        }
        else
        {
          $colnull = "NOT NULL";
        }

        // Default Werte
        if ($cols->getvalue("Default") != '')
        {
          $coldef = "DEFAULT ".$cols->getvalue("Default")." ";
        }
        else
        {
          $coldef = "";
        }

        // Spezial Werte
        $colextra = $cols->getvalue("Extra");
        if ($cols->getvalue("Key") != '')
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
          $con = $cont->getvalue($cols->getvalue("Field"));

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
  $content = "## Redaxo Database Dump Version ".$REX['version']." \n".str_replace("\r", "", $dump);

  return $content;
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
function _rex_a1_add_folder_to_tar(&$tar, $path, $dir)
{
  $handle = opendir($path.$dir);
  $array_indx = 0;
  #$tar->addFile($path.$dir."/",TRUE);
  while (false !== ($file = readdir($handle)))
  {
    $dir_array[$array_indx] = $file;
    $array_indx++;
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