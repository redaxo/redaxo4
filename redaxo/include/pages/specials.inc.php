<?php
/**
 *
 * @package redaxo4
 * @version svn:$Id$
 */

// -------------- Defaults

$subpage = rex_request('subpage', 'string');
$func = rex_request('func', 'string');

// -------------- Header

rex_title($I18N->msg('specials'), $REX['PAGES']['specials']->getPage()->getSubPages());

$content = rex_register_extension_point('PAGE_SPECIALS_OUTPUT', "",
  array(
    'subpage' => $subpage,
  )
);

if($content != "") {
  echo $content;

} else {
  switch($subpage) {
    case 'lang': $file = 'specials.clangs.inc.php'; break;
    default : $file = 'specials.settings.inc.php'; break;
  }

  require $REX['INCLUDE_PATH'].'/pages/'.$file;

}

