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

include $REX['INCLUDE_PATH'] . '/layout/top.php';

$page = rex_request('page', 'string');
$subpage = rex_request('subpage', 'string');

if ($REX['VERSION'] . $REX['SUBVERSION'] < '42')
{
  $I18N->locale = $REX['LANG'];
  $I18N->filename = $REX['INCLUDE_PATH'] . '/addons/tinymce/lang/'. $REX['LANG'] . ".lang";
  $I18N->loadTexts();
}

rex_title($I18N->msg('tinymce_title'), $REX['ADDON'][$page]['SUBPAGES']);

if ($subpage == '')
{
  $subpage = 'help';
}

$incfile = $REX['INCLUDE_PATH'] . '/addons/' . $page . '/pages/' . $subpage . '.inc.php';

include($incfile);

include $REX['INCLUDE_PATH'].'/layout/bottom.php';
