<?php

// CREATE/UPDATE DATABASE AND CREATE/UPDATE MODULES
$sql = new sql();
$sql->query("
CREATE TABLE `rex_12_search_index` (
  `id` int(100) NOT NULL default '0',
  `path` varchar(100) NOT NULL default '',
  `status` tinyint(2) NOT NULL default '0',
  `clang` tinyint(2) NOT NULL default '0',
  `online_from` int(11) NOT NULL default '0',
  `online_to` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `keywords` text NOT NULL,
  `content` text NOT NULL,
  FULLTEXT KEY `full_index` (`name`,`keywords`,`content`),
  FULLTEXT KEY `name` (`name`)
) TYPE=MyISAM;
");


$REX['ADDON']['install']["search_index"] = 1;
// ERRMSG IN CASE: $REX[ADDON][installmsg]["import_export"] = "Leider konnte nichts installiert werden da.";

?>