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

$REX['ADDON']['install']['tinymce'] = 0;

// REDAXO 3.2.3, 4.0.x, 4.1.x - Dateien in Ordner files/addons/ kopieren
if ($REX['VERSION'] . $REX['SUBVERSION'] < '42')
{
  $addon_filesdir = $REX['MEDIAFOLDER'] . '/addons/' . $addonname;
  $addon_filesdir = str_replace('\\', '/', $addon_filesdir);
  if (is_dir($addon_filesdir))
  {
    if(!rex_deleteDir($addon_filesdir, true))
    {
      $REX['ADDON']['installmsg'][$addonname] = 'Verzeichnis '.$addon_filesdir.' konnte nicht gel&ouml;scht werden!';
      $REX['ADDON']['install'][$addonname] = 1;	
    }
  }
}
