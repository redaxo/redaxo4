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

if(!defined('REX_A62_FIELD_TEXT'))
{
  // Feldtypen
  define('REX_A62_FIELD_TEXT',                 1);
  define('REX_A62_FIELD_TEXTAREA',             2);
  define('REX_A62_FIELD_SELECT',               3);
  define('REX_A62_FIELD_RADIO',                4);
  define('REX_A62_FIELD_CHECKBOX',             5);
  define('REX_A62_FIELD_REX_MEDIA_BUTTON',     6);
  define('REX_A62_FIELD_REX_MEDIALIST_BUTTON', 7);
  define('REX_A62_FIELD_REX_LINK_BUTTON',      8);
  define('REX_A62_FIELD_REX_LINKLIST_BUTTON',  9);
  define('REX_A62_FIELD_DATE',                 10);
  define('REX_A62_FIELD_DATETIME',             11);
  define('REX_A62_FIELD_LEGEND',               12);
  
  define('REX_A62_FIELD_COUNT',                12);
}


if ($REX['REDAXO'])
  $I18N_META_INFOS = new i18n($REX['LANG'], $REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/lang');

$REX['ADDON']['rxid'][$mypage] = '62';
$REX['ADDON']['page'][$mypage] = $mypage;
$REX['ADDON']['name'][$mypage] = 'Meta Infos';
$REX['ADDON']['perm'][$mypage] = 'metainfo[]';
$REX['ADDON']['version'][$mypage] = "1.2";
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

  $page = rex_request('page', 'string');
  $mode = rex_request('mode', 'string');

  // additional javascripts
  if($page == 'metainfo' || ($page == 'content' && $mode == 'meta'))
  {
    rex_register_extension('PAGE_HEADER',
      create_function('$params', 'return $params[\'subject\'] .\'  <script src="index.php?js=addons/metainfo" type="text/javascript"></script>\'."\n";')
    );
  }

  require_once ($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/classes/class.rex_table_manager.inc.php');
  require_once ($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/functions/function_metainfo.inc.php');
  require_once ($REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/extensions/extension_common.inc.php');

  // include extensions
  if ($page == 'content' && $mode == 'meta')
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