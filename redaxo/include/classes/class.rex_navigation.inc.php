<?php

/**
 * Klasse zum Erstellen von Navigationen, v0.1
 *
 * @package redaxo4
 * @version $Id: class.rex_navigation.inc.php,v 1.24 2008/03/22 16:06:09 kills Exp $
 */

/*
 * Beispiel:
 *
 * UL, LI Navigation von der Rootebene aus,
 * 2 Ebenen durchgehen, Alle unternavis offen
 * und offline categorien nicht beachten
 *
 * $nav = rex_navigation::factory();
 * $nav->setClasses(array('lev1', 'lev2', 'lev3'));
 * echo $nav->get(0,2,TRUE,TRUE);
 *
 * Sitemap
 *
 * $nav = rex_navigation::factory();
 * $nav->show(0,-1,TRUE,TRUE);
 * 
 * Breadcrump
 * 
 * $nav = rex_navigation::factory();
 * $nav->showBreadcrump(true);
 */

class rex_navigation
{

	var $category_id; // Startpunkt
	var $depth; // Wieviele Ebene tief, ab der Startebene
	var $open; // alles aufgeklappt, z.b. Sitemap
	var $ignore_offlines;
	var $paths = array();
	var $classes = array();

	var $current_article_id = -1; // Aktueller Artikel
	var $current_category_id = -1; // Aktuelle Katgorie

	function rex_navigation()
	{
	}

  function factory()
  {
    static $class = null;

    if(!$class)
    {
      // ----- EXTENSION POINT
      $class = rex_register_extension_point('REX_NAVI_CLASSNAME', 'rex_navigation');
    }

    return new $class();
  }
  
	function get($category_id = 0,$depth = 3,$open = FALSE, $ignore_offlines = false)
	{
    $this->category_id = $category_id;
    $this->depth = $depth;
    $this->open = $open;
    $this->ignore_offlines = $ignore_offlines;
	  
    if(!$this->_setActivePaths()) return FALSE;
		return $this->_getNavigation($this->category_id,$this->ignore_offlines);
	}

	function show($category_id = 0,$depth = 3,$open = FALSE, $ignore_offlines = false)
	{
		echo $this->get($category_id, $depth, $open, $ignore_offlines);
	}
	
	function getBreadcrump($includeCurrent = false, $category_id = 0)
	{
    $this->category_id = $category_id;
    
	  if(!$this->_setActivePaths()) return FALSE;
    
    $return = '';
    $return .= '<ul class="breadcrump">';
    
    if($includeCurrent)
      $this->paths[] = $this->current_article_id;
      
    $i = 1;
    foreach($this->paths as $pathItem)
    {
      $cat = OOCategory::getCategoryById($pathItem);
      $return .= '<li class="lvl'. $i .'"><a href="'. $cat->getUrl() .'">'. htmlspecialchars($cat->getName()) .'</a></li>';
      $i++;
    }
    $return .= '</ul>';
    
    return $return;
	}
	
  function showBreadcrump($category_id = 0, $includeCurrent = false)
  {
    echo $this->getBreadcrump($category_id, $includeCurrent);
  }
  
	function setClasses($classes)
	{
	  $this->classes = $classes;
	}

	function _setActivePaths()
	{
		global $REX;
		$this->current_article_id = $REX["ARTICLE_ID"];

		if($a = OOArticle::getArticleById($REX["ARTICLE_ID"]))
		{
			$this->paths = explode("|",trim($a->getValue("path"), '|'));
			$this->current_category_id = $a->getCategoryId();
			return TRUE;
		}else
		{
			return FALSE;
		}
	}

	function _getNavigation($category_id,$ignore_offlines = TRUE)
	{
	  static $depth = 0;
	  
    if($category_id < 1)
	  	$nav_obj = OOCategory::getRootCategories($ignore_offlines);
		else
	  	$nav_obj = OOCategory::getChildrenById($category_id, $ignore_offlines);

  	$return = "";

		if(count($nav_obj)>0)
		  $return .= '<ul class="navi'. ($depth+1) .'">';

		foreach($nav_obj as $nav)
		{
		  $liClass = '';
		  $linkClass = '';
		  
		  // classes abhaengig vom pfad
			if($nav->getId() == $this->current_category_id)
			{
			  $liClass .= ' active';
			  $linkClass .= ' current';
			}
			elseif (in_array($nav->getId(),$this->paths))
			{
        $liClass .= ' active';
			}
			else
			{
        $liClass .= ' normal';
			}
			
      // classes abhaengig vom level
      if(isset($this->classes[$depth]))
        $liClass .= ' '. $this->classes[$depth];
      
			$liClass   = $liClass   == '' ? '' : ' class="'. ltrim($liClass) .'"';
			$linkClass = $linkClass == '' ? '' : ' class="'. ltrim($linkClass) .'"';
			  
      $return .= '<li'. $liClass .'>';
			$return .= '<a'. $linkClass .' href="'.$nav->getUrl().'">'.htmlspecialchars($nav->getName()).'</a>';

			$depth++;
			if(($this->open || 
			    $nav->getId() == $this->current_category_id || 
			    in_array($nav->getId(),$this->paths))
         && ($this->depth > $depth || $this->depth < 0))
			{
				$return .= $this->_getNavigation($nav->getId(),$ignore_offlines);
			}
			$depth--;

			$return .= '</li>';
		}

		if(count($nav_obj)>0)
	  	$return .= '</ul>';

		return $return;
	}
}