<?php


// class sql 1.0
//
// erstellt 01.12.2001
// pergopa kristinus gbr
// lange strasse 31
// 60311 Frankfurt/M.
// www.pergopa.de
// ersteller: j.kristinus

class sql
{

   var $feld; // array der feldnamen die gesetzt werden
   var $wert; // array der feldwerte die gesetzt werden sollen
   var $zaehler; // wieviele felder existieren
   var $table; // Tabelle setzen
   var $wherevar; // where ...
   var $select; // select:
   var $counter; // select: welcher datensatz ist dran
   var $rows; // select: anzahl der treffer
   var $result; // select: alle angaben gespeichert
   var $last_insert_id;
   var $debugsql;
   var $identifier;
   var $DBID;
   var $insertID;

   var $error; // Fehlertext
   var $errno; // Fehlernummer
   
   function sql($DBID = 1)
   {
      global $DB, $REX;

      $this->identifier = @ mysql_pconnect($DB[$DBID][HOST], $DB[$DBID][LOGIN], $DB[$DBID][PSW]);
      $this->debugsql = false;
      $this->DBID = $DBID;
      $this->selectDB();
      $this->zaehler = 0;
      $this->counter = 0;

      // MySQL Version bestimmen
      if ($REX['MYSQL_VERSION'] == '')
      {
         $this->setQuery('SELECT VERSION() as VERSION');
         $res = $this->get_array();
         $arr = array ();
         preg_match('/([0-9]+\.([0-9\.])+)/', $res[0]['VERSION'], $arr);
         $REX['MYSQL_VERSION'] = $arr[1];
      }
   }

   function selectDB()
   {
      global $DB, $REX;

      if (!@ mysql_select_db($DB[$this->DBID][NAME]))
      {
         echo "<font style='color:red; font-family:verdana,arial; font-size:11px;'>Class SQL 1.1 | Database down. | Please contact <a href=mailto:".$REX[error_emailaddress].">".$REX[error_emailaddress]."</a>\n | Thank you!\n</font>";
         exit;
      }
   }

   function setQuery($select)
   {
      $this->zaehler = 0;
      $this->counter = 0;
      $this->select = $select;
      $this->selectDB();
      $this->result = @ mysql_query("$select");
      $this->rows = @ mysql_num_rows($this->result);
      $this->insertID = @ mysql_insert_id($this->result);
      $this->error = @ mysql_error();
      $this->errno = @ mysql_errno();

      if ($this->debugsql)
      {
         echo '<hr>';
         echo 'Query: '.htmlspecialchars($select).'<br/>';
         
         if ( $this->getRows() != '') {
            echo 'Affected Rows: '. $this->getRows().'<br/>';
         }
         if ($this->getError() != '')
         {
            echo 'Error Message: '.htmlspecialchars( $this->getError()).'<br/>';
            echo 'Error Code: '. $this->getErrno().'<br/>';
         }
      }
   }

   function setTable($table)
   {
      $this->table = $table;
   }

   function setValue($feldname, $wertigkeit)
   {
      $this->feld[$this->zaehler] = $feldname;
      $this->wert[$this->zaehler] = $wertigkeit;
      $this->zaehler++;
   }

   function isValueOf($feld, $prop)
   {

      $value = @ mysql_result($this->result, $this->counter, "$feld");
      if ($prop == "")
      {
         return TRUE;
      }
      else
         if (strstr($value, $prop))
         {
            return TRUE;
         }
         else
         {
            return FALSE;
         }
   }

   function where($where)
   {
      $this->wherevar = "where $where";
   }

   function getValue($value)
   {
      // wenn db verwechslungen, dann hier aktiv setzen
      // $this->selectDB();
      $back = @ mysql_result($this->result, $this->counter, "$value");
      return $back;
   }

   function getRows()
   {
      return $this->rows;
   }

   function nextValue()
   {
      $this->counter++;
   }

   function resetCounter()
   {
      $this->counter = 0;
   }

   function liste()
   {
      $back = "";

      for ($i = 0; $i < $this->getRows(); $i ++)
      {
         for ($j = 0; $j < $this->zaehler; $j ++)
         {
            $back .= $this->getValue($this->feld[$j])." \n";
         }

         $back .= "<br>";
         $this->counter++;
      }

      return $back;
   }

   function update()
   {
      $sql = "";
      for ($i = 0; $i < $this->zaehler; $i ++)
      {
         if ($sql != "")
         {
            $sql .= ",";
         }
         // $sql .= $this->feld[$i]."='".addslashes( $this->wert[$i])."'";
         $sql .= $this->feld[$i]."='". ($this->wert[$i])."'";

      }

      $this->selectDB();
      $this->result = mysql_query("update $this->table set $sql $this->wherevar");
      $this->error = @ mysql_error();
      $this->message = "event updated<br>";
      if ($this->debugsql)
         echo "update $this->table set $sql $this->wherevar";
   }

   function insert()
   {
      $sql1 = "";
      $sql2 = "";
      for ($i = 0; $i < $this->zaehler; $i ++)
      {
         if ($sql1 != "")
         {
            $sql1 .= ",";
         }
         if ($sql2 != "")
         {
            $sql2 .= ",";
         }
         $sql1 .= $this->feld[$i];
         // $sql2 .= "'".addslashes( $this->wert[$i])."'";
         $sql2 .= "'". ($this->wert[$i])."'";
      }

      $this->selectDB();
      $this->result = @ mysql_query("insert into $this->table ($sql1) VALUES ($sql2)");
      $this->last_insert_id = @ mysql_insert_id($this->identifier);
      $this->error = @ mysql_error();
      $this->message = "new event inserted<br>";
      if ($this->debugsql)
         echo htmlspecialchars("insert into $this->table ($sql1) VALUES ($sql2)");
   }

   function delete()
   {
      $this->selectDB();
      $this->result = mysql_query("delete from $this->table $this->wherevar");
      $this->error = @ mysql_error();
   }

   function flush()
   {
      $this->zaehler = 0;
      $this->table = "";
      $this->error = "";
      $this->wherevar = "";
      $this->select = "";
      $this->counter = 0;
      $this->rows = 0;
      $this->result = "";
   }

   function query($sql)
   {
      $this->selectDB();
      $this->result = mysql_query("$sql");
      $this->error = @ mysql_error();
      if ($this->debugsql)
         echo $sql."<br>";
   }

   function escape($string)
   {
      return mysql_escape_string($string);
   }

   function next()
   {
      $this->counter++;
   }

   function getLastID()
   {
      return $this->last_insert_id;
   }

   // GET ARRAY RESULT
   function get_array($sql = "")
   {

      if ($sql != "")
      {
         $this->setQuery($sql);
      }

      $data = null;

      while ($row = @ mysql_fetch_assoc($this->result))
      {
         $data[] = $row;
      }

      return $data;
   }

   function getErrno()
   {
      return $this->errno;
   }
   
   function getError()
   {
      return $this->error;
   }

   function setNewId($field)
   {
      $result = mysql_query("select $field from $this->table order by $field desc LIMIT 1");
      if (@ mysql_num_rows($result) == 0)
         $id = 0;
      else
         $id = mysql_result($result, 0, "$field");
      $id ++;
      $this->setValue($field, $id);
      return $id;
   }

   function getFieldnames()
   {
      $y = mysql_num_fields($this->result);
      for ($x = 0; $x < $y; $x ++)
      {
         $echo[] = mysql_field_name($this->result, $x);
      }
      return $echo;
   }

}
?>