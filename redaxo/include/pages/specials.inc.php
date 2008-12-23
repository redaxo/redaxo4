<?php
/**
 *
 * @package redaxo4
 * @version $Id: specials.inc.php,v 1.3 2008/03/25 10:02:17 kills Exp $
 */

// -------------- Defaults

$subpage = rex_request('subpage', 'string');
$func = rex_request('func', 'string');

// -------------- Header

$subline = array(
  array( '', $I18N->msg('main_preferences')),
  array( 'lang', $I18N->msg('languages')),
);

rex_title($I18N->msg('specials'),$subline);

switch($subpage)
{
  case 'lang': $file = 'specials.clangs.inc.php'; break;
  default : $file = 'specials.settings.inc.php'; break;
}

require $REX['INCLUDE_PATH'].'/pages/'.$file;