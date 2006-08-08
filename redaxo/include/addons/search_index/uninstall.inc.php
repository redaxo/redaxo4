<?php

// DROP/DELETE DATABASE/TABLES AND DELETE MODULES

$sql = new sql();
$sql->query("DROP TABLE IF EXISTS rex_12_search_index");

$REX['ADDON']['install']['search_index'] = 0;
// ERRMSG IN CASE: $REX['ADDON']['installmsg']['search_index'] = "Deinstallation fehlgeschlagen weil...";

?>