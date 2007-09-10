<?php

/**
 * MetaForm Addon
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo3
 * @version $Id$
 */

rex_register_extension('A1_BEFORE_DB_IMPORT', 'rex_a62_metainfo_cleanup');

/**
 * Alle Metafelder löschen, nicht das nach einem Import in der Parameter Tabelle
 * noch Datensätze zu Feldern stehen, welche nicht als Spalten in der
 * rex_article angelegt wurden!
 */
function rex_a62_metainfo_cleanup($params)
{
	global $REX;

  require_once $REX['INCLUDE_PATH'].'/addons/metainfo/classes/class.rex_tableExpander.inc.php';

  $sql = new rex_sql();
  $sql->setQuery('SELECT name FROM ' . $REX['TABLE_PREFIX'] . '62_params');

  for ($i = 0; $i < $sql->getRows(); $i++)
  {
    if (substr($sql->getValue('name'), 0, 4) == 'med_')
      $tableManager = new rex_a62_tableManager($REX['TABLE_PREFIX'] . 'file');
    else
      $tableManager = new rex_a62_tableManager($REX['TABLE_PREFIX'] . 'article');

    $tableManager->deleteColumn($sql->getValue('name'));

    $sql->next();
  }


  // evtl reste aufräumen
  $tablePrefixes = array('article' => array('art_', 'cat_'), 'file' => array('med_'));
  foreach($tablePrefixes as $table => $prefixes)
  {
    $table = $REX['TABLE_PREFIX'] .$table;
    $tableManager = new rex_a62_tableManager($table);

    foreach(rex_sql::showColumns($table) as $column)
    {
      $column = $column['name'];
      if(in_array(substr($column, 0, 4), $prefixes))
      {
        $tableManager->deleteColumn($column);
      }
    }
  }

  $sql = new rex_sql();
  $sql->setQuery('DELETE FROM '. $REX['TABLE_PREFIX'] .'62_params');
}

?>