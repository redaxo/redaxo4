<?php
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
      $article_path = $REX['HTDOCS_PATH']."redaxo/include/generated/articles/".$article_id.".".$clang.".article";
      if (file_exists($article_path))
      {
         include ($article_path);
      }
      else
      {
         return null;
      }
      if ($OOCategory)
      {
         return new OOCategory(OORedaxo :: convertGeneratedArray($REX['ART'][$article_id], $clang));
      }
      else
      {
         return new OOArticle(OORedaxo :: convertGeneratedArray($REX['ART'][$article_id], $clang));
      }

   }

   /**
    * CLASS Function:
    * Return a list of articles which names match the
    * search string. For now the search string can be either
    * a simple name or a string containing SQL search placeholders
    * that you would insert into a 'LIKE '%...%' statement.
    *
    * Returns an array of OORedaxo objects.
    */
   function searchArticlesByName($article_name, $ignore_offlines = false, $clang = false, $categories = false)
   {
      global $REX;
      if ($clang === false)
         $clang = $REX['CUR_CLANG'];
      $offline = $ignore_offlines ? " and status = 1 " : "";
      $cats = '';
      if (is_array($categories))
      {
         $cats = " and re_id in (".implode(',', $categories).") ";
      }
      elseif (is_string($categories))
      {
         $cats = " and re_id = $categories ";
      }elseif ( $categories === true) {
         $cats = " and startpage = 1 ";
      }

      $artlist = array ();
      $sql = new sql;
//              $sql->debugsql = true;
      $sql->setQuery("select ".implode(',', OORedaxo :: getClassVars())." from rex_article where name like '$article_name' AND clang='$clang' $offline $cats");
      for ($i = 0; $i < $sql->getRows(); $i ++)
      {
         foreach (OORedaxo :: getClassVars() as $var)
         {
            $article_data[$var] = $sql->getValue($var);
         }
         $artlist[] = new OOArticle($article_data, $clang);
         $sql->next();
      }
      return $artlist;
   }

   /**
    * CLASS Function:
    * Return a list of articles which have a certain type.
    *
    * Returns an array of OORedaxo objects.
    */
   function searchArticlesByType($article_type_id, $ignore_offlines = false, $clang = false)
   {
      global $REX;
      if ($clang === false)
         $clang = $REX['CUR_CLANG'];
      $offline = $ignore_offlines ? " and status = 1 " : "";
      $artlist = array ();
      $sql = new sql;
      $sql->setQuery("select ".implode(',', OORedaxo :: getClassVars())." FROM rex_article WHERE type_id = '$article_type_id' AND clang='$clang' $offline");
      for ($i = 0; $i < $sql->getRows(); $i ++)
      {
         foreach (OORedaxo :: getClassVars() as $var)
         {
            $article_data[$var] = $sql->getValue($var);
         }
         $artlist[] = new OOArticle($article_data, $clang);
         $sql->next();
      }
      return $artlist;
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
      return OOArticle :: getArticleById($REX['STARTARTIKEL_ID'], $clang);
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
         $clang = $GLOBALS['REX']['CUR_CLANG'];
      $articlelist = $REX['HTDOCS_PATH']."redaxo/include/generated/articles/".$a_category_id.".".$clang.".alist";
      $artlist = array ();
      if (file_exists($articlelist))
      {
         include ($articlelist);
         if (is_array($REX['RE_ID'][$a_category_id]))
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
   function getCategoryId() {
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
   
   /**
    *  Accessor Method:
    * returns true if this Article is the Startpage for the category.
    * @deprecated
    */
   function isStartPage()
   {
      return $this->isStartArticle();
   }

   /**
    *  Accessor Method:
    * returns true if this Article is the Startpage for the category.
    */
   function isStartArticle()
   {
      return $this->_startpage;
   }
   
   /**
    *  Accessor Method:
    * returns true if this Article is the Startpage for the entire site.
    */
   function isSiteStartArticle()
   {
      global $REX;
      return $this->_id == $REX['STARTARTIKEL_ID'];
   }
   
   function isValid($article)
   {
      return is_object($article) && is_a($article, 'ooarticle');
   }
}
?>