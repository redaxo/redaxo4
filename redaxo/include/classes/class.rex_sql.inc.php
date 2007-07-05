<?php

/**
 * Klasse zur Verbindung und Interatkion mit der Datenbank
 * @version $Id$ 
 */

class rex_sql
{
  var $values; // Werte von setValue

  var $table; // Tabelle setzen
  var $wherevar; // WHERE Bediengung
  var $query; // letzter Query String
  var $counter; // ResultSet Cursor
  var $rows; // anzahl der treffer
  var $result; // ResultSet
  var $last_insert_id; // zuletzt angelegte auto_increment nummer
  var $debugsql; // debug schalter
  var $identifier; // Datenbankverbindung
  var $DBID; // ID der Verbindung

  var $error; // Fehlertext
  var $errno; // Fehlernummer

  function rex_sql($DBID = 1)
  {
    global $REX;

		// Baue eine Verbindung via mysql_pconnect auf
		// Falls das Fehl schlägt, verbindung über mysql_connect aufbauen
		// Bei manchen Providern ist mysql_pconnect nicht aktiviert/freigeschaltet
    $this->identifier = @mysql_pconnect($REX['DB'][$DBID]['HOST'], $REX['DB'][$DBID]['LOGIN'], $REX['DB'][$DBID]['PSW']);
    if(!$this->identifier)
			$this->identifier = @mysql_connect($REX['DB'][$DBID]['HOST'], $REX['DB'][$DBID]['LOGIN'], $REX['DB'][$DBID]['PSW']);     
    										
    $this->debugsql = false;
    $this->DBID = $DBID;
    $this->selectDB();
    $this->counter = 0;
    
    // MySQL Version bestimmen
    if ($REX['MYSQL_VERSION'] == '')
    {
      $this->setQuery('SET SQL_MODE=""');
      $res = $this->getArray('SELECT VERSION() as VERSION');
      if(preg_match('/([0-9]+\.([0-9\.])+)/', $res[0]['VERSION'], $matches))
      {
        $REX['MYSQL_VERSION'] = $matches[1];
      }
      else
      {
        exit('Could not identifiy MySQL Version!');
      }
    }
  }

  /**
   * Stellt die Verbindung zur Datenbank her
   */
  function selectDB()
  {
    global $REX;

    if (!@ mysql_select_db($REX['DB'][$this->DBID]['NAME'], $this->identifier))
    {
      echo "<font style='color:red; font-family:verdana,arial; font-size:11px;'>Class SQL 1.1 | Database down. | Please contact <a href=mailto:" . $REX['ERROR_EMAIL'] . ">" . $REX['ERROR_EMAIL'] . "</a>\n | Thank you!\n</font>";
      exit;
    }
  }

  /**
   * Setzt eine Abfrage (SQL) ab
   * @param $query Abfrage 
   */
  function setQuery($qry)
  {
    $qry = trim($qry);
    $this->counter = 0;
    $this->last_insert_id = 0;
    $this->query = $qry;
    $this->result = @ mysql_query($qry, $this->identifier);

    if ($this->result)
    {
      if (preg_match('/^\s*?(SELECT|SHOW|UPDATE|INSERT|DELETE|REPLACE)/i', $qry, $matches))
      {
        switch (strtoupper($matches[1]))
        {
          case 'SELECT' :
          case 'SHOW' :
          {
            $this->rows = mysql_num_rows($this->result);
            break;
          }
          case 'REPLACE' :
          case 'DELETE' :
          case 'UPDATE' :
          {
            $this->rows = mysql_affected_rows($this->identifier);
            break;
          }
          case 'INSERT' :
          {
            $this->rows = mysql_affected_rows($this->identifier);
            $this->last_insert_id = mysql_insert_id($this->identifier);
            break;
          }
        }
      }
      $this->error = '';
      $this->errno = '';
    }
    else
    {
      $this->error = mysql_error($this->identifier);
      $this->errno = mysql_errno($this->identifier);
    }

    if ($this->debugsql || $this->error != '')
    {
      $this->printError($qry);
    }
    
    return $this->getError() === '';
  }

  /**
   * Setzt den Tabellennamen
   * @param $table Tabellenname
   */
  function setTable($table)
  {
    $this->table = $table;
  }

  /**
   * Setzt den Wert eine Spalte
   * @param $feldname Spaltenname
   * @param $wert Wert
   */
  function setValue($feldname, $wert)
  {
    $this->values[$feldname] = $wert;
  }

  /**
   * Prüft den Wert einer Spalte der aktuellen Zeile ob ein Wert enthalten ist
   * @param $feld Spaltenname des zu prüfenden Feldes
   * @param $prop Wert, der enthalten sein soll
   */
  function isValueOf($feld, $prop)
  {
    if ($prop == "")
    {
      return TRUE;
    }
    else
    {
      return strpos($this->getValue($feld), $prop) !== false;
    }
  }

  /**
   * Setzt die WHERE Bedienung der Abfrage
   */
  function setWhere($where)
  {
    $this->wherevar = "WHERE $where";
  }

  /**
   * Gibt den Wert einer Spalte im ResultSet zurück
   * @param $value Name der Spalte
   * @param [$row] Zeile aus dem ResultSet
   */
  function getValue($feldname, $row = null)
  {
  	if(isset($this->values[$feldname]))
  		return $this->values[$feldname];
  		
    $_row = $this->counter;
    if (is_int($row))
    {
      $_row = $row;
    }

    $res = mysql_result($this->result, $_row, $feldname);
    if($res === false && function_exists('debug_backtrace'))
    {
      $trace = debug_backtrace();
      $loc = $trace[0];
      echo '<b>Warning</b>:  mysql_result(): Error found in file <b>'. $loc['file'] .'</b> on line <b>'. $loc['line'] .'</b><br />';
    }
    return $res; 
  }

  /**
   * Gibt die Anzahl der Zeilen zurück
   */
  function getRows()
  {
    return $this->rows;
  }

  /**
   * Gibt die Anzahl der Felder/Spalten zurück
   */
  function getFields()
  {
  	return mysql_num_fields($this->result);
  }
  
  /**
   * Baut den SET bestandteil mit der 
   * verfügbaren values zusammen und gibt diesen zurück
   * 
   * @see setValue 
   */
  function buildSetQuery()
  {
    $qry = '';
    if (is_array($this->values))
    {
      foreach ($this->values as $fld_name => $value)
      {
        if ($qry != '')
        {
          $qry .= ',';
        }
        
        // Bei <tabelle>.<feld> Notation '.' ersetzen, da sonst `<tabelle>.<feld>` entsteht 
        if(strpos($fld_name, '.') !== false)
          $fld_name = str_replace('.', '`.`', $fld_name);
          
        $qry .= '`' . $fld_name . '`="' . $value .'"';
// Da Werte via POST/GET schon mit magic_quotes escaped werden, 
// brauchen wir hier nicht mehr escapen
//        $qry .= '`' . $fld_name . '`=' . $this->escape($value);
      }
    }

    return $qry;
  }

  /**
   * Setzt eine Update-Anweisung auf die angegebene Tabelle 
   * mit den angegebenen Werten und WHERE Parametern ab
   * 
   * @see #setTable()
   * @see #setValue()
   * @see #where()
   */
  function update()
  {
    $this->setQuery('UPDATE `' . $this->table . '` SET ' . $this->buildSetQuery() .' '. $this->wherevar);
    return $this->getError() === '';
  }

  /**
   * Setzt eine Insert-Anweisung auf die angegebene Tabelle 
   * mit den angegebenen Werten ab
   * 
   * @see #setTable()
   * @see #setValue()
   */
  function insert()
  {
    $this->setQuery('INSERT INTO `' . $this->table . '` SET ' . $this->buildSetQuery() .' '. $this->wherevar);
    return $this->getError() === '';
  }

  /**
   * Setzt eine Replace-Anweisung auf die angegebene Tabelle 
   * mit den angegebenen Werten ab
   * 
   * @see #setTable()
   * @see #setValue()
   */
  function replace()
  {
    $this->setQuery('REPLACE INTO `' . $this->table . '` SET ' . $this->buildSetQuery() .' '. $this->wherevar);
    return $this->getError() === '';
  }

  /**
   * Setzt eine Delete-Anweisung auf die angegebene Tabelle 
   * mit den angegebenen WHERE Parametern ab
   * 
   * @see #setTable()
   * @see #where()
   */
  function delete()
  {
    $this->setQuery('DELETE FROM `' . $this->table . '` ' . $this->wherevar);
    return $this->getError() === '';
  }

  /**
   * Stellt alle Werte auf den Ursprungszustand zurück
   */
  function flush()
  {
    $this->table = '';
    $this->error = '';
    $this->errno = '';
    $this->wherevar = '';
    $this->query = '';
    $this->counter = 0;
    $this->rows = 0;
    $this->result = '';
    $this->values = array ();
  }

  /**
   * Setzt den Cursor des Resultsets auf die nächst höhere Stelle
   */
  function previous()
  {
    $this->counter--;
  }

  /**
   * Setzt den Cursor des Resultsets auf die nächst höhere Stelle
   */
  function next()
  {
    $this->counter++;
  }
  
  /**
   * Setzt den Cursor des Resultsets zurück zum Anfang
   */
  function reset()
  {
    $this->counter = 0;
  }
	
  /**
   * Gibt die letzte InsertId zurück
   */
  function getLastId()
  {
    return $this->last_insert_id;
  }
  
  /**
   * Lädt das komplette Resultset in ein Array und gibts dieses zurück 
   */
  function getArray($sql = "", $fetch_type = MYSQL_ASSOC)
  {
    if ($sql != "")
    {
      $this->setQuery($sql);
    }

    $data = array();

    while ($row = @ mysql_fetch_array($this->result, $fetch_type))
    {
      $data[] = $row;
    }

    return $data;
  }

  /**
   * Gibt die zuletzt aufgetretene Fehlernummer zurück 
   */
  function getErrno()
  {
    return $this->errno;
  }

  /**
   * Gibt den zuletzt aufgetretene Fehlernummer zurück 
   */
  function getError()
  {
    return $this->error;
  }

  /**
   * Gibt die letzte Fehlermeldung aus 
   */
  function printError($select)
  {
    if ($this->debugsql === 2 && strlen($this->getError()) > 0 || $this->debugsql == true)
    {
      echo '<hr />' . "\n";
      echo 'Query: ' . nl2br(htmlspecialchars($select)) . "<br />\n";

      if (strlen($this->getRows()) > 0)
      {
        echo 'Affected Rows: ' . $this->getRows() . "<br />\n";
      }
      if (strlen($this->getError()) > 0)
      {
        echo 'Error Message: ' . htmlspecialchars($this->getError()) . "<br />\n";
        echo 'Error Code: ' . $this->getErrno() . "<br />\n";
      }
    }
  }

  /**
   * Setzt eine Spalte auf den nächst möglich auto_increment Wert
   * @param $field Name der Spalte 
   */
  function setNewId($field)
  {
    $this->setQuery('SELECT `' . $field . '` FROM `' . $this->table . '` ORDER BY `' . $field . '` DESC LIMIT 1');

    if ($this->getRows() == 0)
      $id = 0;
    else
      $id = mysql_result($this->result, 0, $field);

    $id++;
    $this->setValue($field, $id);

    return $id;
  }
  
  /**
   * Gibt die Spaltennamen des ResultSets zurück 
   */
  function getFieldnames()
  {
    $fields = array ();
    for ($i = 0; $i < $this->getFields(); $i++)
    {
      $fields[] = mysql_field_name($this->result, $i);
    }
    return $fields;
  }

  /**
   * Escaped den übergeben Wert für den DB Query
   */
  function escape($value)
  {
    // Quote if not a number or a numeric string
    if (!is_numeric($value))
    {
      $value = "'" . mysql_real_escape_string($value, $this->identifier) . "'";
    }
    return $value;
  }
  
  /**
   * Gibt die Serverversion zurück
   */
  function getServerVersion()
  {
    global $REX;
    return $REX['MYSQL_VERSION']; 
  }

  /**
   * Gibt ein SQL Singelton Objekt zurück 
   */
  function getInstance()
  {
    static $instance;

    if ($instance)
      $instance->flush();
    else
      $instance = new rex_sql();

    return $instance;
  }
  
  function disconnect()
  {
    if($this->identifier)
      mysql_close($this->identifier);
  } 
}
?>