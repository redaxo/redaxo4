<?php

/**
 * MetaForm Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4
 * @version $Id: uninstall.inc.php,v 1.2 2008/03/24 12:31:57 kills Exp $
 */

$REX['ADDON']['install']['metainfo'] = 0;
// ERRMSG IN CASE: $REX['ADDON']['installmsg']['metainfo'] = "Deinstallation fehlgeschlagen weil...";

require_once ($REX['INCLUDE_PATH'] . '/addons/metainfo/extensions/extension_cleanup.inc.php');

rex_a62_metainfo_cleanup(array('force' => true));

?>