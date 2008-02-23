<?php
/**
 * Backend Search Addon
 *
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 *
 * @package redaxo4
 * @version $Id$
 */

function rex_a256_search_mpool_menu($params)
{
  global $I18N_BE_SEARCH, $I18N;

  $subline = $params['subject'];

  $newSubline = array();
  foreach($subline as $entry)
  {
    $newSubline[] = $entry;
    if($entry[1] == $I18N->msg('pool_file_list'))
    {
      $newSubline[] = array('search', $I18N_BE_SEARCH->msg('search_mpool_search'));
    }
  }

  return $newSubline;
}

function rex_a256_search_mpool($params)
{
  global $I18N_BE_SEARCH;

  if(rex_request('subpage', 'string') != 'search') return $params['subject'];

  $subject = $params['subject'];

  $form =
   '  <form method="post">
        <input type="hidden" name="a256_clang" id="rex-a256-article-clang" value="'. $clang .'" />

        <div class="rex-f-lft">
          <label for="rex-a256-article-name">'. $I18N_BE_SEARCH->msg('search_article_name') .'</label>
          <input type="text" name="a256_article_name" id="rex-a256-article-name" />

          <label for="rex-a256-article-id">'. $I18N_BE_SEARCH->msg('search_article_id') .'</label>
          <input type="text" name="a256_article_id" id="rex-a256-article-id" />
          <input type="submit" name="" value="'. $I18N_BE_SEARCH->msg('search_start') .'" />
        </div>

        <div class="rex-f-rght">
          <label for="rex-a256-category-id">'. $I18N_BE_SEARCH->msg('search_quick_navi') .'</label>
          <noscript>
            <input type="submit" name="" value="'. $I18N_BE_SEARCH->msg('search_jump_to_category') .'" />
          </noscript>
        </div>
      </form>';

  $search_bar = $message.
  '<div id="rex-a256-searchbar">
     '. $form .'
     '. $search_result .'
   </div>
   <div class="rex-clearer"></div>';

  return $search_bar . $subject;
}
?>