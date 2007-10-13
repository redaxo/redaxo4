<?php

/**
 * MetaForm Addon
 *
 * @author staab[at]public-4u[dot]de Markus Staab
 *
 * @package redaxo4
 * @version $Id$
 */

$mypage = 'metainfo';

if ($REX['REDAXO'])
  $I18N_META_INFOS = new i18n($REX['LANG'], $REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/lang');

$REX['ADDON']['rxid'][$mypage] = '62';
$REX['ADDON']['page'][$mypage] = $mypage;
$REX['ADDON']['name'][$mypage] = 'Meta Infos';
$REX['ADDON']['perm'][$mypage] = 'metainfo[]';
$REX['ADDON']['version'][$mypage] = "1.0";
$REX['ADDON']['author'][$mypage] = "Markus Staab, Jan Kristinus";
// $REX['ADDON']['supportpage'][$mypage] = "";

$REX['PERM'][] = 'metainfo[]';

if ($REX['REDAXO'])
{
  // Include Extensions
  if (isset ($page))
  {
    require_once ($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/extensions/extension_common.inc.php');

    if ($page == 'content' && isset ($mode) && $mode == 'meta')
    {
      require_once ($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/extensions/extension_art_metainfo.inc.php');
    }
    elseif ($page == 'structure')
    {
      require_once ($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/extensions/extension_cat_metainfo.inc.php');
    }
    elseif ($page == 'medienpool' && isset ($subpage) && $subpage == 'detail')
    {
      require_once ($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/extensions/extension_med_metainfo.inc.php');
    }
    elseif ($page == 'import_export')
    {
      require_once ($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/extensions/extension_cleanup.inc.php');
    }
  }
}

?>