<?php
class OOStructure
{
  /*
   * Public Attribute 
   */

  // clang der auszugebenden struktur
  var $clang;
  // level limitierung (-1 = kein Limit)
  var $depth_limit;
  // Kategorien ingorieren?
  var $ignore_categories;
  // Startartikel ignorieren?
  var $ignore_startarticles;
  // Artikel ignorieren?
  var $ignore_articles;
  // offline Artikel ignorieren?
  var $ignore_offlines;
  // Artikel "ohne namen" ignorieren?
  var $ignore_empty;
  // Kategory die als Wurzelverzeichnis genutzt werden soll
  // (int, object, oder array aus object u./o. int)  
  var $root_category;

  // Tag des umgebenden Elements
  var $main_tag;
  // Tag von Eltern-Elementen
  var $parent_tag;
  // Tag der Kind-Elemente
  var $child_tag;

  // Attribute des umgebenden Elements
  var $main_attr;
  // Attribute von Eltern-Elementen
  var $parent_attr;
  // Attribute von Kind-Elementen
  var $child_attr;

  // Link für die Elemente
  var $link;
  // Spacer für Artikel "ohne namen"
  var $empty_value;

  /*
   * Private Attribute 
   */

  // aktueller level der Ausgabe 
  var $_depth;

  function OOStructure($main_attr = '', $parent_attr = '', $child_attr = '')
  {
    $this->_depth = 0;
    $this->depth_limit = -1;
    $this->ignore_categories = false;
    $this->ignore_startarticles = false;
    $this->ignore_articles = false;
    $this->ignore_offlines = false;
    $this->ignore_empty = false;
    $this->clang = false;

    $this->root_category = null;

    $this->main_tag = 'ul';
    $this->parent_tag = 'ul';
    $this->child_tag = 'li';

    $this->main_attr = $main_attr != '' ? ' '.$main_attr : '';
    $this->parent_attr = $parent_attr != '' ? ' '.$parent_attr : '';
    $this->child_attr = $child_attr != '' ? ' '.$child_attr : '';

    $this->empty_value = '&nbsp;';
    $this->link = 'javascript:void(0);';
  }

  function _formatNodeValue($name, & $node)
  {
    return $node->toLink();
  }

  function _formatNode($node)
  {
    $name = $node->getName();

    if ($name == '')
    {
      if ($this->ignore_empty)
      {
        return '';
      }
      else
      {
        $name = $this->empty_value;
      }
    }

    if ($this->depth_limit > 0 && $this->_depth >= $this->depth_limit)
    {
      return '';
    }

    $s = '';
    // Kategorien ingorieren?
    if (OOCategory :: isValid($node) && !$this->ignore_categories || OOArticle :: isValid($node) && !($this->ignore_startarticles && $node->isStartPage()))
    {

      $s .= '<'.$this->child_tag.$this->child_attr.'>';
      $s .= $this->_formatNodeValue($name, $node);

      if (OOCategory :: isValid($node))
      {
        $childs = $node->getChildren($this->ignore_offlines, $this->clang);
        $articles = $node->getArticles($this->ignore_offlines, $this->clang);

        if (is_array($childs) && count($childs) > 0 || is_array($articles) && count($articles) > 0 && !$this->ignore_articles)
        {
          $this->_depth++;
          $s .= '<'.$this->parent_tag.$this->parent_attr.'>';

          if (is_array($childs))
          {
            foreach ($childs as $child)
            {
              $s .= $this->_formatNode($child);
            }
          }

          // Artikel ingorieren?
          if (!$this->ignore_articles)
          {
            if (is_array($articles))
            {
              foreach ($articles as $article)
              {
//                if ($article->isStartPage())
//                {
//                  continue;
//                }

                $s .= '<'.$this->child_tag.$this->child_attr.'>';
                $s .= $this->_formatNodeValue($article->getName(), $article);
                $s .= '</'.$this->child_tag.'>';
              }
            }
          }

          $s .= '</'.$this->parent_tag.'>';
          $this->_depth--;
        }
      }

      $s .= '</'.$this->child_tag.'>';
    }
    return $s;
  }

  function getStructure()
  {
    $s = '';
    $this->_depth = 0;

    if ($this->root_category === null)
    {
      $root_nodes = OOCategory :: getRootCategories($this->ignore_offlines, $this->clang);
    }
    else
    {
      if (is_int($this->root_category) && $this->root_category === 0)
      {
        $root_nodes = OOArticle :: getRootArticles($this->ignore_offlines, $this->clang);
      }
      else
      {
        $root_nodes = array ();
        $root_category = OOCategory :: _getCategoryObject($this->root_category);
        // Rootkategorien selbst nicht anzeigen, nur deren Kind-Elemente
        if (is_array($root_category))
        {
          foreach ($root_category as $root_cat)
          {
            $this->appendChilds($root_cat, $root_nodes);
            $this->appendArticles($root_cat, $root_nodes);
          }
        }
        else
        {
          $this->appendChilds($root_category, $root_nodes);
          $this->appendArticles($root_category, $root_nodes);
        }
      }
    }

    if (is_array($root_nodes))
    {
      $s .= '<'.$this->main_tag.$this->main_attr.'>';

      foreach ($root_nodes as $node)
      {
        $s .= $this->_formatNode($node);
      }

      $s .= '</'.$this->main_tag.'>';
    }

    return $s;
  }

  function & appendChilds(& $source, & $target)
  {
    $childs = $source->getChildren($this->ignore_offlines, $this->clang);
    if (is_array($childs))
    {
      foreach ($childs as $child)
      {
        $target[] = $child;
      }
    }
  }

  function & appendArticles(& $source, & $target)
  {
    $articles = $source->getArticles($this->ignore_offlines, $this->clang);
    if (is_array($articles))
    {
      foreach ($articles as $article)
      {
        $target[] = $article;
      }
    }
  }

  function printStructure()
  {
    echo $this->getStructure();
  }
}

class OODivStructure extends OOStructure
{
  function OODivStructure()
  {
    $this->OOStructure();
    $this->main_tag = 'div';
    $this->parent_tag = 'div';
    $this->child_tag = 'div';
  }
}
?>