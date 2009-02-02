<?php

/**
 * Klasse zum erstellen von Navigationen
 *
 * @package redaxo4
 * @version $Id: class.rex_navigation.inc.php,v 1.24 2008/03/22 16:06:09 kills Exp $
 */

/*
Beispiel:




*/

class rex_navigation
{

  var $ul = 'ul';
  var $li = 'li';
  var $active = 'active';
  var $passive = 'passive';
  var $current = 'current';
  var $normal = 'normal';


  /**
   * Erstellt ein rex_list Objekt
   *
   * @param $query SELECT Statement
   * @param $rowsPerPage Anzahl der Elemente pro Zeile
   * @param $listName Name der Liste
   */
  function rex_navigation($query, $rowsPerPage = 30, $listName = null, $debug = false)
  {
    global $REX, $I18N;

  }

  function factory($query, $rowsPerPage = 30, $listName = null, $debug = false)
  {
    static $class = null;

    return new $class($query, $rowsPerPage, $listName, $debug);
  }

  function init()
  {
    // nichts tun
  }

  function get()
  {
    global $I18N;


    return;
  }

  function show()
  {
    echo $this->get();
  }
}