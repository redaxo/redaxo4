<?php

function rex_a256_highlight_hit($string, $needle)
{
  return preg_replace(
    '/(.*)('. preg_quote($needle, '/') .')(.*)/i',
    '\\1<span class="a256-search-hit">\\2</span>\\3',
    $string
  );
}
