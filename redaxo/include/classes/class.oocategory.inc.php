<?php

/**
 * Object Oriented Framework: Bildet eine Kategorie der Struktur ab
 * @package redaxo4
 * @version $Id: class.oocategory.inc.php,v 1.2 2008/03/07 17:51:09 kills Exp $
 */

class OOCategory extends OORedaxo
{

  function OOCategory($params = false, $clang = false)
  {
    parent :: OORedaxo($params, $clang);
  }

  /*
   * CLASS Function:
   * Return an OORedaxo object based on an id
   */
  function getCategoryById($category_id = false, $clang = false)
  {
    global $REX;

    if ($clang === false)
      $clang = $REX['CUR_CLANG'];

    return OOArticle :: getArticleById($category_id, $clang, true);
  }

  /*
   * CLASS Function:
   * Return all Children by id
   */
  function getChildrenById($cat_parent_id, $ignore_offlines = false, $clang = false)
  {
    global $REX;

    if ($clang === false)
      $clang = $REX['CUR_CLANG'];

    $categorylist = $REX['INCLUDE_PATH']."/generated/articles/".$cat_parent_id.".".$clang.".clist";

    $catlist = array ();

    if (!file_exists($categorylist))
    {
    	include_once ($REX["INCLUDE_PATH"]."/functions/function_rex_generate.inc.php");
    	rex_generateLists($cat_parent_id);
    }

    if (file_exists($categorylist))
    {
      include ($categorylist);
      if (isset ($REX['RE_CAT_ID'][$cat_parent_id]) and is_array($REX['RE_CAT_ID'][$cat_parent_id]))
      {
        foreach ($REX['RE_CAT_ID'][$cat_parent_id] as $var)
        {
          $category = OOCategory :: getCategoryById($var, $clang);
          if ($ignore_offlines)
          {
            if ($category->isOnline())
            {
              $catlist[] = $category;
            }
          }
          else
          {
            $catlist[] = $category;
          }
        }
      }
    }

    return $catlist;
  }

  /*
   * Accessor Method:
   * returns the article priority
   */
  function getPriority()
  {
    return $this->_catprior;
  }

  /**
   * CLASS Function:
   * Return a list of top level categories, ie.
   * categories that have no parent.
   * Returns an array of OOCategory objects sorted by $prior.
   *
   * If $ignore_offlines is set to TRUE,
   * all categories with status 0 will be
   * excempt from this list!
   */
  function getRootCategories($ignore_offlines = false, $clang = false)
  {
    global $REX;

    if ($clang === false)
      $clang = $REX['CUR_CLANG'];

    return OOCategory :: getChildrenById(0, $ignore_offlines, $clang);
  }

  /*
   * Object Function:
   * Return a list of all subcategories.
   * Returns an array of OORedaxo objects sorted by $prior.
   *
   * If $ignore_offlines is set to TRUE,
   * all categories with status 0 will be
   * excempt from this list!
   */
  function getChildren($ignore_offlines = false, $clang = false)
  {
    if ($clang === false)
      $clang = $this->_clang;

    return OOCategory :: getChildrenById($this->_id, $ignore_offlines, $clang);
  }

  /*
   * Object Function:
   * Returns the parent category
   */
  function getParent($clang = false)
  {
    if ($clang === false)
      $clang = $this->_clang;

    return OOCategory :: getCategoryById($this->_re_id, $clang);
  }

  /*
   * Object Function:
   * Returns TRUE if this category is the direct
   * parent of the other category.
   */
  function isParent($other_cat)
  {
     return $this->getId() == $other_cat->getParentId() &&
            $this->getClang() == $other_cat->getClang();
  }

  /*
   * Object Function:
   * Returns TRUE if this category is an ancestor
   * (parent, grandparent, greatgrandparent, etc)
   * of the other category.
   */
  function isAncestor($other_cat)
  {
    $category = OOCategory :: _getCategoryObject($other_cat);
    $expl = explode('|', $category->_path);
    if ($expl[1] != "")
    {
      if (in_array($this->_id, $expl))
      {
        return true;
      }
    }

    return false;
  }

  /*
   * Object Function:
   * Return a list of articles in this category
   * Returns an array of OOArticle objects sorted by $prior.
   *
   * If $ignore_offlines is set to TRUE,
   * all articles with status 0 will be
   * excempt from this list!
   */
  function getArticles($ignore_offlines = false)
  {
    return OOArticle :: getArticlesOfCategory($this->_id, $ignore_offlines, $this->_clang);
  }

  /*
   * Object Function:
   * Return the start article for this category
   */
  function getStartArticle()
  {
    return OOArticle :: getCategoryStartArticle($this->_id, $this->_clang);
  }

  /*
   * Accessor Method:
   * returns the name of the article
   */
  function getName()
  {
    return $this->_catname;
  }

  function & _getCategoryObject($category, $clang = false)
  {
    if (is_object($category))
    {
      return $category;
    }
    elseif (is_int($category))
    {
      return OOCategory :: getCategoryById($category, $clang);
    }
    elseif (is_array($category))
    {
      $catlist = array ();
      foreach ($category as $cat)
      {
        $catobj = OOCategory :: _getCategoryObject($cat, $clang);
        if (is_object($catobj))
        {
          $catlist[] = $catobj;
        }
        else
        {
          return null;
        }
      }
      return $catlist;
    }
    return null;
  }

  /*
   * Static Method: Returns boolean if is category
   */
  function isValid($category)
  {
    return is_object($category) && is_a($category, 'oocategory');
  }
}
?>