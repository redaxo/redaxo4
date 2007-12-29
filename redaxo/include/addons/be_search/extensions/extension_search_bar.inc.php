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

function rex_a256_search_bar($params)
{
  global $REX, $I18N_BE_SEARCH, $category_id, $clang;

  $search_result = '';
  $editUrl = 'index.php?page=content&article_id=%s&mode=edit&clang=%s';

  // ------------ Parameter
  $a256_article_id   = rex_post('a256_article_id'  , 'int');
  $a256_clang        = rex_post('a256_clang'       , 'int');
  $a256_article_name = rex_post('a256_article_name', 'string');

  // ------------ Suche via ArtikelId
  if($a256_article_id != 0)
  {
    $OOArt = OOArticle::getArticleById($a256_article_id, $a256_clang);
    if(OOArticle::isValid($OOArt))
    {
      header('Location:'. sprintf($editUrl, $a256_article_id, $a256_clang));
      exit();
    }
  }

  // ------------ Suche via ArtikelName
  if($a256_article_name != '')
  {
    $qry = '
    SELECT id
    FROM '. $REX['TABLE_PREFIX'] .'article
    WHERE
      clang = '. $a256_clang .' AND
      (
        name LIKE "%'. $a256_article_name .'%" OR
        catname LIKE "%'. $a256_article_name .'%"
      )';

    $search = new rex_sql();
//    $search->debugsql = true;
    $search->setQuery($qry);
    $foundRows = $search->getRows();

    if($foundRows > 0)
    {
      $search_result .= '<ul>';
      for($i = 0; $i < $foundRows; $i++)
      {
        $OOArt = OOArticle::getArticleById($search->getValue('id'), $a256_clang);
        $search_result .= '<li><a href="'. sprintf($editUrl, $search->getValue('id'), $a256_clang) .'">'. $OOArt->getName() .'</a></li>';
        $search->next();
      }
      $search_result .= '</ul>';
    }
  }

  $subject = $params['subject'];

  $category_select = new rex_category_select();
  $category_select->setName('category_id');
  $category_select->setId('rex-a256_category_id');
  $category_select->setSize('1');
  $category_select->setSelected($category_id);

  $form =
   '  <form method="post">
        <input type="hidden" name="a256_clang" id="rex-a256-article_id" value="'. $clang .'" />

        <label for="rex-a256-article_name">'. $I18N_BE_SEARCH->msg('search_article_name') .'</label>
        <input type="text" name="a256_article_name" id="rex-a256-article_name" />

        <label for="rex-a256-article_id">'. $I18N_BE_SEARCH->msg('search_article_id') .'</label>
        <input type="text" name="a256_article_id" id="rex-a256-article_id" />
        <input type="submit" name="" value="'. $I18N_BE_SEARCH->msg('search_start') .'" />

        <label for="rex-a256_category_id">'. $I18N_BE_SEARCH->msg('search_quick_navi') .'</label>
        '. $category_select->get() . '
        <noscript>
          <input type="submit" name="" value="'. $I18N_BE_SEARCH->msg('search_jump_to_category') .'" />
        </noscript>
      </form>';

  $search_bar =
  '<div id="rex-a256-searchbar">
     '. $form .'
     '. $search_result .'
   </div>';

  return $search_bar . $subject;
}
?>