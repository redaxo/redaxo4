<?php

/**
 * MetaForm Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4
 * @version $Id: install.inc.php,v 1.2 2008/03/24 12:31:57 kills Exp $
 */

$error = '';

require_once ($REX['INCLUDE_PATH'] . '/addons/metainfo/extensions/extension_cleanup.inc.php');
rex_a62_metainfo_cleanup(array('force' => true));

// uninstall ausführen, damit die db clean ist vorm neuen install
$uninstall = $REX['INCLUDE_PATH'] . '/addons/metainfo/uninstall.sql';
rex_install_dump($uninstall);

if ($error != '')
  $REX['ADDON']['installmsg']['metainfo'] = $error;
else
  $REX['ADDON']['install']['metainfo'] = true;