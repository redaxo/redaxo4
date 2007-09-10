<?php
require_once $REX['INCLUDE_PATH'].'/addons/metainfo/classes/class.rex_tableExpander.inc.php';

$REX['ADDON']['install']['metainfo'] = 0;
// ERRMSG IN CASE: $REX['ADDON']['installmsg']['metainfo'] = "Deinstallation fehlgeschlagen weil...";

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

?>