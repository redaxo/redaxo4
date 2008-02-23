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

function rex_a256_search_mpool($params)
{
  global $I18N_BE_SEARCH;

  $media_name = rex_request('a256_media_name', 'string');
  if(rex_request('subpage', 'string') != '') return $params['subject'];

  $subject = $params['subject'];

  $search_form = '
  <p>
    <label for="a256_media_name">'. $I18N_BE_SEARCH->msg('search_mpool_media') .'</label>
    <input type="text" name="a256_media_name" id="a256_media_name" value="'. $media_name .'" />
  </p>';

  $subject = str_replace('</p>', '</p>'. $search_form, $subject);

  return $subject;
}

function rex_a256_search_mpool_query($params)
{
  global $REX;

  $media_name = rex_request('a256_media_name', 'string');
  if($media_name == '') return $params['subject'];

  $subject = $params['subject'];

  return "SELECT *
          FROM ".$REX['TABLE_PREFIX']."file f, ".$REX['TABLE_PREFIX']."file_category c
          WHERE f.category_id = c.id AND c.path LIKE '%|". $params['category_id'] ."|%' AND (filename LIKE '%". $media_name ."%' OR title LIKE '%". $media_name ."%')
          ORDER BY f.updatedate desc";
}

?>