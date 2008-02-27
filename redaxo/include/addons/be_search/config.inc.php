<?php

/**
 * Backend Search Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 * @package redaxo4
 * @version $Id$
 */

$mypage = 'be_search';

/* Addon Parameter */
$REX['ADDON']['rxid'][$mypage] = '256';
$REX['ADDON']['page'][$mypage] = $mypage;
//$REX['ADDON']['name'][$mypage] = 'Backend Search';
//$REX['ADDON']['perm'][$mypage] = 'be_search[]';
$REX['ADDON']['version'][$mypage] = '1.0';
$REX['ADDON']['author'][$mypage] = 'Markus Staab';
$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';

$REX['EXTPERM'][] = 'be_search[medienpool]';
$REX['EXTPERM'][] = 'be_search[structure]';

if ($REX['REDAXO'])
{
  if(rex_get('css', 'string') == 'addons/be_search')
  {
    $cssfile = $REX['INCLUDE_PATH'] .'/addons/be_search/css/be_search.css';
    rex_send_file($cssfile, 'text/css');
    exit();
  }

  rex_register_extension('PAGE_HEADER',
    create_function('$params', 'return \'  <link rel="stylesheet" type="text/css" href="index.php?css=addons/be_search" />\';')
  );

  $I18N_BE_SEARCH = new i18n($REX['LANG'], $REX['INCLUDE_PATH'] . '/addons/' . $mypage . '/lang');

  // Include Extensions
  if(!isset($page) || $page == '' || $page == 'structure')
  {
    require_once $REX['INCLUDE_PATH'].'/addons/be_search/extensions/extension_search_structure.inc.php';
    rex_register_extension('PAGE_STRUCTURE_HEADER', 'rex_a256_search_structure');
  }
  elseif($page == 'content')
  {
    require_once $REX['INCLUDE_PATH'].'/addons/be_search/extensions/extension_search_structure.inc.php';
    rex_register_extension('PAGE_CONTENT_HEADER', 'rex_a256_search_structure');
  }
  elseif ($page == 'medienpool')
  {
    require_once $REX['INCLUDE_PATH'].'/addons/be_search/extensions/extension_search_mpool.inc.php';
    rex_register_extension('MEDIA_LIST_TOOLBAR', 'rex_a256_search_mpool');
    rex_register_extension('MEDIA_LIST_QUERY', 'rex_a256_search_mpool_query');
  }
}
?>