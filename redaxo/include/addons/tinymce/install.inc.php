<?php

/**
 * TinyMCE Addon
 *
 * @author andreaseberhard[at]gmail[dot]com Andreas Eberhard
 * @author <a href="http://www.redaxo.de">www.redaxo.de</a>
 *
 * @package redaxo4
 * @version svn:$Id$
 */

$addonname = 'tinymce';

$error = '';

// REDAXO 3.2.3, 4.0.x, 4.1.x - Dateien in Ordner files/addons/ kopieren
if ($REX['VERSION'] . $REX['SUBVERSION'] < '42')
{
  include($REX['INCLUDE_PATH'] . '/addons/' . $addonname . '/functions/functions.inc.php');
  $source_dir = $REX['INCLUDE_PATH'] . '/addons/' . $addonname . '/files';
  $dest_dir = $REX['MEDIAFOLDER'] . '/addons/' . $addonname;
  $start_dir = $REX['MEDIAFOLDER'] . '/addons';

  if (is_dir($source_dir))
  {
    if (!is_dir($start_dir))
    {
      mkdir($start_dir);
    }
    if(!rex_copyDir($source_dir, $dest_dir , $start_dir))
    {
      $error = 'Verzeichnis '.$source_dir.' konnte nicht nach '.$dest_dir.' kopiert werden!';
    }
  }
}

if ($error != '')
{
  $REX['ADDON']['installmsg'][$addonname] = $error;
  $REX['ADDON']['install'][$addonname] = false;
}
else
{
  $REX['ADDON']['install'][$addonname] = true;
}