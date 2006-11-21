<?php

/**
 * Klassen zum erhalten der Rückwärtskompatibilität
 * Dieser werden beim nächsten Versionssprung entfallen
 * @version $Id$ 
 */

// rex_sql -> sql alias
class sql extends rex_sql
{
	var $select;
	
  function sql($DBID = 1)
  {
    parent::rex_sql($DBID);
    // Altes feld wurde umbenannt, deshalb hier als Alias speichern
    $this->select =& $this->query;
  }
  
  function get_array($sql = "", $fetch_type = MYSQL_ASSOC)
  {
    return $this->getArray($sql, $fetch_type);
  }

  function getLastID()
  {
    return $this->getLastId();
  }
  
  /**
   * Setzt den Cursor des Resultsets auf die nächst höhere Stelle
   * @see #next();
   */
  function nextValue()
  {
  	$this->next();
  }

  /**
   * Setzt den Cursor des Resultsets zurück zum Anfang
   */
  function resetCounter()
  {
    $this->reset();
  }

  /**
   * Setzt die WHERE Bedienung der Abfrage
   */
  function where($where)
  {
    $this->setWhere($where);
  }
  
  /**
   * Sendet eine Abfrage an die Datenbank
   */
  function query($qry)
  {
    return $this->setQuery($qry);
  }
}

// rex_select -> select alias
class select extends rex_select{

  function select()
  {
    parent::select();
  }
}

// rex_article -> article alias
class article extends rex_article{

  function article($article_id = null, $clang = null)
  {
    parent::rex_article($article_id, $clang);
  }
}


// ----------------------------------------- Functions

// rex_getUrl -> getUrlById alias
function getUrlByid($id, $clang = "", $params = "")
{
  return rex_getUrl($id, $clang, $params);
}

// rex_title -> title alias
function title($head, $subtitle = '', $styleclass = "grey", $width = '770px')
{
  return rex_title($head, $subtitle, $styleclass, $width);
}

?>