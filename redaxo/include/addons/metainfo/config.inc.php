<?php

/**
 * MetaForm Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4
 * @version $Id: config.inc.php,v 1.7 2008/03/26 18:54:34 kills Exp $
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
$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';
$REX['ADDON']['prefixes'][$mypage] = array('art_', 'cat_', 'med_');
$REX['ADDON']['metaTables'][$mypage] = array(
  'art_' => $REX['TABLE_PREFIX'] .'article',
  'cat_' => $REX['TABLE_PREFIX'] .'article',
  'med_' => $REX['TABLE_PREFIX'] .'file',
);

$REX['PERM'][] = 'metainfo[]';

if ($REX['REDAXO'])
{
  if(rex_get('js', 'string') == 'addons/metainfo')
  {
    $jsfile = $REX['INCLUDE_PATH'] .'/addons/metainfo/js/metainfo.js';
    rex_send_file($jsfile, 'text/javascript');
    exit();
  }

  // Include Extensions
  if (isset ($page))
  {
    if($page == 'metainfo')
    {
      rex_register_extension('PAGE_HEADER',
        create_function('$params', 'return $params[\'subject\'] .\'  <script src="index.php?js=addons/metainfo" type="text/javascript"></script>\'."\n";')
      );
    }

    require_once ($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/classes/class.rex_table_manager.inc.php');
    require_once ($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/functions/function_metainfo.inc.php');
    require_once ($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/extensions/extension_common.inc.php');

    if ($page == 'content' && isset ($mode) && $mode == 'meta')
    {
      require_once ($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/extensions/extension_art_metainfo.inc.php');
    }
    elseif ($page == 'structure')
    {
      require_once ($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/extensions/extension_cat_metainfo.inc.php');
    }
    elseif ($page == 'medienpool')
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