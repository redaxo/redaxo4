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
  var $select; // letzter Query String
  var $counter; // select: welcher datensatz ist dran
  var $rows; // select: anzahl der treffer
  var $result; // select: alle angaben gespeichert
  var $last_insert_id; // zuletzt angelegte auto_increment nummer
  var $debugsql; // debug schalter
  var $identifier; // Datenbankverbindung
  var $DBID; // ID der Verbindung

  var $error; // Fehlertext
  var $errno; // Fehlernummer

  function rex_sql($DBID = 1)
  {
    global $REX;

    $this->identifier = @ mysql_pconnect($REX['DB'][$DBID]['HOST'], $REX['DB'][$DBID]['LOGIN'], $REX['DB'][$DBID]['PSW']);
    $this->debugsql = false;
    $this->DBID = $DBID;
    $this->selectDB();
    $this->zaehler = 0;
    $this->counter = 0;
    
    // MySQL Version bestimmen
    if ($REX['MYSQL_VERSION'] == '')
    {
      $res = $this->get_array('SELECT VERSION() as VERSION');
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
    $this->counter = 0;
    $this->last_insert_id = 0;
    $this->select = $qry;
    $this->result = @ mysql_query($qry, $this->identifier);

    if ($this->result)
    {
      if (preg_match('/^\s*?(SELECT|SHOW|UPDATE|INSERT)/i', $qry, $matches))
      {
        switch (strtoupper($matches[1]))
        {
          case 'SELECT' :
          case 'SHOW' :
            {
              $this->rows = mysql_num_rows($this->result);
              break;
            }
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

    if ($this->debugsql)
    {
      $this->printError($qry);
    }
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
  function where($where)
  {
    $this->wherevar = "where $where";
  }

  /**
   * Gibt den Wert einer Spalte im ResultSet zurück
   * @param $value Name der Spalte
   * @param [$row] Zeile aus dem ResultSet
   */
  function getValue($value, $row = null)
  {
    $_row = $this->counter;
    if (is_int($row))
    {
      $_row = $row;
    }

    return @ mysql_result($this->result, $_row, $value);
  }

  /**
   * Gibt die Anzahl der Zeilen zurück, die vom letzten SQL betroffen sind
   */
  function getRows()
  {
    return $this->rows;
  }

  /**
   * Setzt den Cursor des Resultsets auf die nächst höhere Stelle
   * @see #next();
   */
  function nextValue()
  {
    $this->counter++;
  }

  /**
   * Setzt den Cursor des Resultsets zurück zum Anfang
   */
  function resetCounter()
  {
    $this->counter = 0;
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
        $qry .= '`' . $fld_name . '`=\'' . $value . '\'';
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
    $this->setQuery('UPDATE `' . $this->table . '` SET ' . $this->buildSetQuery() . $this->wherevar);
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
    $this->setQuery('INSERT INTO `' . $this->table . '` SET ' . $this->buildSetQuery() . $this->wherevar);
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
    $this->setQuery('REPLACE INTO `' . $this->table . '` SET ' . $this->buildSetQuery() . $this->wherevar);
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
  }

  /**
   * Stellt alle Werte auf den Ursprungszustand zurück
   */
  function flush()
  {
    $this->table = "";
    $this->error = "";
    $this->errno = "";
    $this->wherevar = "";
    $this->select = "";
    $this->counter = 0;
    $this->rows = 0;
    $this->result = "";
    $this->values = array ();
  }

  /**
   * Sendet eine Abfrage an die Datenbank
   * @deprecated 3.3 - 21.08.2006
   */
  function query($qry)
  {
    $this->setQuery($qry);
  }

  /**
   * Setzt den Cursor des Resultsets auf die nächst höhere Stelle
   */
  function next()
  {
    $this->counter++;
  }

  /**
   * Gibt die letzte InsertId zurück
   */
  function getLastID()
  {
    return $this->last_insert_id;
  }

  /**
   * Lädt das komplette Resultset in ein Array und gibts dieses zurück 
   */
  function get_array($sql = "", $fetch_type = MYSQL_ASSOC)
  {

    if ($sql != "")
    {
      $this->setQuery($sql);
    }

    $data = null;

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
    if ($this->debugsql === 2 && strlen($this->getError()) > 0 || $this->debugsql === true)
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
    $numFields = mysql_num_fields($this->result);
    for ($i = 0; $i < $numFields; $i++)
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
      $value = "'" . mysql_real_escape_string($value) . "'";
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
      $instance = new sql();

    return $instance;
  }
}
?>