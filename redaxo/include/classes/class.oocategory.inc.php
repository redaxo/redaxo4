<?php
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
      $clang = $GLOBALS['REX']['CUR_CLANG'];
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
      $clang = $GLOBALS['REX']['CUR_CLANG'];
    $categorylist = $REX['HTDOCS_PATH']."redaxo/include/generated/articles/".$cat_parent_id.".".$clang.".clist";
    $catlist = array ();
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
   * CLASS Function:
   * Return a list of categories which names match the
   * search string. For now the search string can be either
   * a simple name or a string containing SQL search placeholders
   * that you would insert into a 'LIKE '%...%' statement.
   *
   * Returns an array of OORedaxo objects.
   */
  function searchCategoriesByName($a_name, $ignore_offlines = false, $clang = false)
  {
    return OOArticle :: searchArticlesByName($a_name, $ignore_offlines, $clang, true);
  }

  /*
   * CLASS Function:
   * Return a list of top level categories, ie.
   * categories that have no parent.
   * Returns an array of OOCategory objects sorted by $prior.
   *
   * If $ignore_offlines is set to TRUE,
   * all categories with status 0 will be
   * excempt from this list!
   */
  //function getRootCategories($ignore_offlines = false) {

  function getRootCategories($ignore_offlines = false, $clang = false)
  {
    global $REX;
    if ($clang === false)
      $clang = $GLOBALS['REX']['CUR_CLANG'];
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
    global $REX;
    if ($clang === false)
      $clang = $GLOBALS['REX']['CUR_CLANG'];
    return OOCategory :: getChildrenById($this->_id, $ignore_offlines, $clang);
  }

  /*
   * Object Function:
   * Returns the parent category
   */
  function getParent()
  {
    return OOCategory :: getCategoryById($this->_re_id);
  }

  /*
   * Object Function:
   * Returns TRUE if this category is the direct
   * parent of the other category.
   */
  function isParent($other_cat)
  {

    // return $this->_id == $other_cat->_re_category_id;
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

    if ($this->_id == $other_cat_id)
      return true;

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
  function getArticles($ignore_offlines = true, $clang = false)
  {
    return OOArticle :: getArticlesOfCategory($this->_id, $ignore_offlines, $clang);
  }

  /*
   * Object Function:
   * Return the start article for this category
   */
  function getStartArticle()
  {
    return OOArticle :: getCategoryStartArticle($this->_id);
  }

  /*
   * Accessor Method:
   * returns the name of the article
   */
  function getName()
  {
    return $this->_catname;
  }

  function & _getCategoryObject($category)
  {
    if (is_object($category))
    {
      return $category;
    }
    elseif (is_int($category))
    {
      return OOCategory :: getCategoryById($category);
    }
    elseif (is_array($category))
    {
      $catlist = array ();
      foreach ($category as $cat)
      {
        $catobj = OOCategory :: _getCategoryObject($cat);
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
  
   function isValid($category)
   {
      return is_object($category) && is_a($category, 'oocategory');
   }
}
?>