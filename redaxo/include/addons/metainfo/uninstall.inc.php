<?php

$REX['ADDON']['install']['metainfo'] = 0;
// ERRMSG IN CASE: $REX['ADDON']['installmsg']['metainfo'] = "Deinstallation fehlgeschlagen weil...";

require_once ($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/extensions/extension_cleanup.inc.php');

rex_a62_metainfo_cleanup(array());

?>