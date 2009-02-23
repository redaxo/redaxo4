<?php

/**
 * Hebt einen Suchtreffer $neelde im Suchergebnis $string hervor
 * 
 * @param $params
 */
function rex_a256_highlight_hit($string, $needle)
{
  return preg_replace(
    '/(.*)('. preg_quote($needle, '/') .')(.*)/i',
    '\\1<span class="a256-search-hit">\\2</span>\\3',
    $string
  );
}

/**
 * Bindet ggf extensions ein
 * 
 * @param $params
 */
function rex_a256_extensions_handler($params)
{
  global $REX;
  
  $page = $params['subject'];
  
  // Include Extensions
  if($page == 'structure')
  {
    require_once $REX['INCLUDE_PATH'].'/addons/be_search/extensions/extension_search_structure.inc.php';
    rex_register_extension('PAGE_STRUCTURE_HEADER', 'rex_a256_search_structure');
  }
  elseif($page == 'content')
  {
    require_once $REX['INCLUDE_PATH'].'/addons/be_search/extensions/extension_search_structure.inc.php';
    rex_register_extension('PAGE_CONTENT_HEADER', 'rex_a256_search_structure');
  }
  elseif ($page == 'mediapool')
  {
    require_once $REX['INCLUDE_PATH'].'/addons/be_search/extensions/extension_search_mpool.inc.php';
    rex_register_extension('MEDIA_LIST_TOOLBAR', 'rex_a256_search_mpool');
    rex_register_extension('MEDIA_LIST_QUERY', 'rex_a256_search_mpool_query');
  }
}