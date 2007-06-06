<?php
$REX['ADDON']['install']['metainfo'] = 0;
// ERRMSG IN CASE: $REX['ADDON']['installmsg']['metainfo'] = "Deinstallation fehlgeschlagen weil...";

$sql = new rex_sql();
$sql->setQuery('SELECT name FROM ' . $REX['TABLE_PREFIX'] . '62_params');

$del = new rex_sql();

for ($i = 0; $i < $sql->getRows(); $i++)
{
  if (substr($sql->getValue('name'), 0, 4) == 'med_')
  {
    $del->setQuery('ALTER TABLE ' . $REX['TABLE_PREFIX'] . 'file DROP ' . $sql->getValue('name'));
  }
  else
  {
    $del->setQuery('ALTER TABLE ' . $REX['TABLE_PREFIX'] . 'article DROP ' . $sql->getValue('name'));
  }
  $sql->next();
}
?>