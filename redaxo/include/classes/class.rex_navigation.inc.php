<?php

/**
 * Klasse zum Erstellen von Navigationen, v0.1
 *
 * @package redaxo4
 * @version $Id: class.rex_navigation.inc.php,v 1.24 2008/03/22 16:06:09 kills Exp $
 */

/*
 * TODOS:
 * 
 * - Breadcrump
 * 
 */

/*
 * Beispiel:
 * 
 * UL, LI Navigation von der Rootebene aus, 
 * 2 Ebenen durchgehen, Alle unternavis offen 
 * und offline categorien nicht beachten
 * 
 * $nav = rex_navigation::factory(0,2,TRUE,TRUE);
 * 
 * Sitemap
 * 
 * $nav = rex_navigation::factory(0,1000,TRUE,TRUE);
 * 
*/

class rex_navigation
{

  var $category_id = 0; // Startpunkt
  var $deep = 3; // Wieviele Ebene tief, ab der Startebene
  var $open = FALSE; // alles aufgeklappt, z.b. Sitemap
  var $ignore_offlines = false;
	var $paths = array();

	var $current_article_id = -1; // Aktueller Artikel
	var $current_category_id = -1; // Aktuelle Katgorie

  function rex_navigation($category_id = 0,$deep = 3,$open = FALSE, $ignore_offlines = false)
  {
    global $REX, $I18N;
    $this->category_id = $category_id;
    $this->deep = $deep;
    $this->open = $open;
    $this->ignore_offlines = $ignore_offlines;
  }

  public function get()
  {
    global $REX,$I18N;
		if(!$this->_setActivePaths()) return FALSE;
    return $this->_getNavigation($this->category_id,0,$this->ignore_offlines);
  }

  public function show()
  {
    echo $this->get();
  }

	private function _setActivePaths()
	{
		global $REX;
		$this->current_article_id = $REX["ARTICLE_ID"];
		
		if($a = OOArticle::getArticleById($REX["ARTICLE_ID"]))
		{
			$this->paths = explode("|",$a->getValue("path"));
			$this->current_category_id = $a->getCategoryId();
			var_dump($this->paths);
			return TRUE;
		}else
		{
			return FALSE;
		}
	}

	private function _getNavigation($category_id,$cur_deep = 0,$ignore_offlines = TRUE)
	{

		if($category_id < 1) 
    	$nav_obj = OOCategory::getRootCategories($ignore_offlines);
    else 
    	$nav_obj = OOCategory::getChildrenById($category_id, $ignore_offlines);
		
		$return = "";

		if(count($nav_obj)>0)
			$return .= '<ul class="navi'.$cur_deep.'">';

    foreach($nav_obj as $nav)
		{
			$path1 = 1;
			
			if($nav->getId() == $this->current_category_id)
				$return .= '<li class="active">XXX<a href="'.$nav->getUrl().'">'.$nav->getName().'</a>';
			elseif (in_array($nav->getId(),$this->paths))
					$return .= '<li class="active">YYY<a class="current" href="'.$nav->getUrl().'">'.$nav->getName().'</a>';
      else
         $return .= '<li class="normal"><a href="'.$nav->getUrl().'">'.$nav->getName().'</a>';
      
	    $cur_deep++;
      if(	
      		($this->open && $this->deep > $cur_deep) || 
      		($nav->getId() == $this->current_category_id && $this->deep > $cur_deep ) ||
      		(in_array($nav->getId(),$this->paths) && $this->deep > $cur_deep)
      	)
      {
      	$return .= $this->_getNavigation($nav->getId(),$cur_deep,$ignore_offlines);
      }
	    $cur_deep--;
      
      $return .= '</li>';
		}		

		if(count($nav_obj)>0)
			$return .= '</ul>';

		return $return;
	}

}