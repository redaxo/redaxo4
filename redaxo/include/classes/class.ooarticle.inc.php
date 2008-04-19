<?php

/**
 * Object Oriented Framework: Bildet einen Artikel der Struktur ab
 * @package redaxo4
 * @version $Id: class.ooarticle.inc.php,v 1.2 2008/03/07 17:51:08 kills Exp $
 */

class OOArticle extends OORedaxo
{

  function OOArticle($params = false, $clang = false)
  {
    parent :: OORedaxo($params, $clang);
  }

  /**
   * CLASS Function:
   * Return an OORedaxo object based on an id
   */
  function getArticleById($article_id, $clang = false, $OOCategory = false)
  {
    global $REX;
    if ($clang === false)
      $clang = $REX['CUR_CLANG'];
    elseif(!isset($REX['CLANG'][$clang])) $clang = 0;

    $article_path = $REX['INCLUDE_PATH'].'/generated/articles/'.$article_id.'.'.$clang.'.article';
    if (!file_exists($article_path))
		{
			include_once ($REX['INCLUDE_PATH'].'/functions/function_rex_generate.inc.php');
    	$article_id = (int) $article_id;
    	rex_generateArticle($article_id, FALSE);
		}

    if (file_exists($article_path))
    {
      include ($article_path);
    }else
    {
			return null;
    }

    if ($OOCategory)
      return new OOCategory(OORedaxo :: convertGeneratedArray($REX['ART'][$article_id], $clang));
    else
      return new OOArticle(OORedaxo :: convertGeneratedArray($REX['ART'][$article_id], $clang));
  }

  /**
   * CLASS Function:
   * Return the site wide start article
   */
  function getSiteStartArticle($clang = false)
  {
    global $REX;
    if ($clang === false)
      $clang = $REX['CUR_CLANG'];
    return OOArticle :: getArticleById($REX['START_ARTICLE_ID'], $clang);
  }

  /**
   * CLASS Function:
   * Return start article for a certain category
   */
  function getCategoryStartArticle($a_category_id, $clang = false)
  {
    global $REX;
    if ($clang === false)
      $clang = $GLOBALS['REX']['CUR_CLANG'];
    return OOArticle :: getArticleById($a_category_id, $clang);
  }

  /**
   * CLASS Function:
   * Return a list of articles for a certain category
   */
  function getArticlesOfCategory($a_category_id, $ignore_offlines = false, $clang = false)
  {
    global $REX;

    if ($clang === false)
      $clang = $REX['CUR_CLANG'];

    if(!isset($REX['RE_ID'][$a_category_id]))
    {
	    $articlelist = $REX['INCLUDE_PATH']."/generated/articles/".$a_category_id.".".$clang.".alist";
	    if (file_exists($articlelist))
	      include($articlelist);
    }

    $artlist = array ();
    if(isset($REX['RE_ID'][$a_category_id]))
    {
	    foreach ($REX['RE_ID'][$a_category_id] as $var)
	    {
	      $article = OOArticle :: getArticleById($var, $clang);
	      if ($ignore_offlines)
	      {
	        if ($article->isOnline())
	        {
	          $artlist[] = $article;
	        }
	      }
	      else
	      {
	        $artlist[] = $article;
	      }
	    }
    }

    return $artlist;
  }

  /**
   * CLASS Function:
   * Return a list of top-level articles
   */
  function getRootArticles($ignore_offlines = false, $clang = false)
  {
    global $REX;
    if ($clang === false)
      $clang = $GLOBALS['REX']['CUR_CLANG'];
    return OOArticle :: getArticlesOfCategory(0, $ignore_offlines, $clang);
  }

  /**
   * Accessor Method:
   * returns the category id
   */
  function getCategoryId()
  {
    return $this->isStartPage() ? $this->getId() : $this->getParentId();
  }

  /*
   * Object Function:
   * Returns the parent category
   */
  function getCategory()
  {
    return OOCategory :: getCategoryById($this->getCategoryId());
  }

  /*
   * Static Method: Returns boolean if is article
   */
  function isValid($article)
  {
    return is_object($article) && is_a($article, 'ooarticle');
  }


  function getValue($value)
  {
    // alias für re_id -> category_id
    if(in_array($value, array('re_id', '_re_id', 'category_id', '_category_id')))
    {
      // für die CatId hier den Getter verwenden,
      // da dort je nach ArtikelTyp unterscheidungen getroffen werden müssen
      return $this->getCategoryId();
    }
    return parent::getValue($value);
  }
}
?>