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

$mypage = 'tinymce';

// Versionsnummer, auch in den Language-Files ändern
$REX['ADDON']['version'][$mypage] = '2.0.0';

// Fix für REDAXO < 4.2.x
if (!isset($REX['FRONTEND_FILE'])) 
{
  $REX['FRONTEND_FILE'] = 'index.php';
}
  
// Backend
if ($REX['REDAXO'])
{

  if (!isset($I18N))
  {
    $I18N = new i18n($REX['LANG'],$REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/lang/');
  }
  
  // I18N, Addon-Titel für die Navigation
  if (isset($I18N) && is_object($I18N))
  {
    if ($REX['VERSION'] . $REX['SUBVERSION'] < '42')
    {
      $I18N->locale = $REX['LANG'];
      $I18N->filename = $REX['INCLUDE_PATH'] . '/addons/tinymce/lang/'. $REX['LANG'] . ".lang";
      $I18N->loadTexts();
    }
    else
    {
      $I18N->appendFile($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/lang/');
    }  
    $REX['ADDON']['page'][$mypage] = $mypage;
    $REX['ADDON']['name'][$mypage] = $I18N->msg('tinymce_menu_link');
  }

  // Addoninfos, Perms usw.
  $REX['ADDON']['perm'][$mypage] = $mypage.'[]';

  $REX['ADDON']['version'][$mypage] = $I18N->msg('tinymce_version');
  $REX['ADDON']['author'][$mypage] = 'Andreas Eberhard';
  $REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';
  $REX['PERM'][] = $mypage.'[]';

  // Subpages
  $REX['ADDON'][$mypage]['SUBPAGES'] = array();
  $REX['ADDON'][$mypage]['SUBPAGES'][] = array ('', $I18N->msg('tinymce_menu_info'));
  $REX['ADDON'][$mypage]['SUBPAGES'][] = array ('settings', $I18N->msg('tinymce_menu_settings'));
  $REX['ADDON'][$mypage]['SUBPAGES'][] = array ('profiles', $I18N->msg('tinymce_menu_profiles'));
  $REX['ADDON'][$mypage]['SUBPAGES'][] = array ('css', $I18N->msg('tinymce_menu_css'));
}


// Konfiguration

// --- DYN
$REX['ADDON']['tinymce']['backend'] = '1';
$REX['ADDON']['tinymce']['frontend'] = '1';
$REX['ADDON']['tinymce']['excludecats'] = 'tinymce';
$REX['ADDON']['tinymce']['excludeids'] = 'a356_ajax';
// --- /DYN


// Include Functions
include($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/functions/functions.inc.php');

// Request page/tinymce
$page = rex_request('page', 'string', '');
if ($page === 'medienpool')
{
  $page = 'mediapool';
}
$tinymce = rex_request('tinymce', 'string', '');

// OUTPUT_FILTER - TinyMCE-Scripte einbinden, Mediapool + Linkmap anpassen
if (($REX['REDAXO'] and $REX['ADDON']['tinymce']['backend'] === '1') or (!$REX['REDAXO'] and $REX['ADDON']['tinymce']['frontend'] === '1'))
{
  rex_register_extension('OUTPUT_FILTER', 'tinymce_output_filter');
}

// Extension-Point für Hinzufügen+übernehmen
if ((($page === 'mediapool') or ($page === 'linkmap')) and ( $tinymce === 'true'))
{
  rex_register_extension('OUTPUT_FILTER', 'tinymce_opf_media_linkmap');
  rex_register_extension('MEDIA_ADDED', 'tinymce_media_added');
}

// JavaScript für Backend und Frontend generieren
// Einbindung TinyMCE mit verschiedenen Profilen
if (rex_request('tinymceinit', 'string', '') === 'true')
{
  tinymce_generate_script();
}

// JavaScript für Mediapool generieren
if (rex_request('tinymcemedia', 'string', '') === 'true')
{
  tinymce_generate_mediascript();
}

// JavaScript für Linkmap generieren
if (rex_request('tinymcelink', 'string', '') === 'true')
{
  tinymce_generate_linkscript();
}

// CSS generieren
if (rex_request('tinymcecss', 'string', '') === 'true')
{
  tinymce_generate_css();
}

// Ausgabe Images
if (rex_request('tinymceimg', 'string', '') <> '')
{
  tinymce_generate_image();
}
