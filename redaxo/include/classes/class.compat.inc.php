<?php

/**
 * Klasse zur Verbindung und Interatkion mit der Datenbank
 * @version $Id$ 
 */

class sql extends rex_sql{

  function sql($DBID = 1)
  {
    parent::rex_sql($DBID);
  }
}

class select extends rex_select{

  function select()
  {
    parent:select();
  }
}

// ----------------------------------------- Redaxo 2.* functions

function getUrlByid($id, $clang = "", $params = "")
{
  return rex_getUrl($id, $clang, $params);
}



?>