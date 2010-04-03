<?php

/**
 * Klasse zum Erstellen von Navigationen, v0.1
 *
 * @package redaxo4
 * @version svn:$Id$
 */

/*
 * Beispiel:
 *
 * UL, LI Navigation von der Rootebene aus,
 * 2 Ebenen durchgehen, Alle unternavis offen
 * und offline categorien nicht beachten
 *
 * Navigation:
 * 
 * $nav = rex_navigation::factory();
 * $nav->setClasses(array('lev1', 'lev2', 'lev3'));
 * $nav->setLinkClasses(array('alev1', 'alev2', 'alev3'));
 * echo $nav->get(0,2,TRUE,TRUE);
 *
 * Sitemap:
 *
 * $nav = rex_navigation::factory();
 * $nav->show(0,-1,TRUE,TRUE);
 * 
 * Breadcrump:
 * 
 * $nav = rex_navigation::factory();
 * $nav->showBreadcrumb(true);
 */

class rex_navigation
{
	var $depth; // Wieviele Ebene tief, ab der Startebene
	var $open; // alles aufgeklappt, z.b. Sitemap
	var $ignore_offlines;
	var $path = array();
	var $classes = array();

	var $current_article_id = -1; // Aktueller Artikel
	var $current_category_id = -1; // Aktuelle Katgorie

	/*private*/ function rex_navigation()
	{
	  // nichts zu tun
	}

  /*public*/ function factory()
  {
    static $class = null;

    if(!$class)
    {
      // ----- EXTENSION POINT
      $class = rex_register_extension_point('REX_NAVI_CLASSNAME', 'rex_navigation');
    }

    return new $class();
  }
  
  /**
   * Generiert eine Navigation
   * 
   * @param $category_id Id der Wurzelkategorie
   * @param $depth Anzahl der Ebenen die angezeigt werden sollen
   * @param $open True, wenn nur Elemente der aktiven Kategorie angezeigt werden sollen, sonst FALSE
   * @param $ignore_offlines FALSE, wenn offline Elemente angezeigt werden, sonst TRUE
   */
	/*public*/ function get($category_id = 0,$depth = 3,$open = FALSE, $ignore_offlines = FALSE)
	{
    if(!$this->_setActivePath()) return FALSE;
    
	  $this->depth = $depth;
    $this->open = $open;
    $this->ignore_offlines = $ignore_offlines;
	  
		return $this->_getNavigation($category_id,$this->ignore_offlines);
	}

  /**
   * @see get()
   */
	/*public*/ function show($category_id = 0,$depth = 3,$open = FALSE, $ignore_offlines = FALSE)
	{
		echo $this->get($category_id, $depth, $open, $ignore_offlines);
	}
	
  /**
   * Generiert eine Breadcrumb-Navigation
   * 
   * @param $startPageLabel Label der Startseite, falls FALSE keine Start-Page anzeigen
   * @param $includeCurrent True wenn der aktuelle Artikel enthalten sein soll, sonst FALSE
   * @param $category_id Id der Wurzelkategorie
   */
	/*public*/ function getBreadcrumb($startPageLabel, $includeCurrent = FALSE, $category_id = 0)
	{
	  if(!$this->_setActivePath()) return FALSE;
	  
	  global $REX;
    
	  $path = $this->path;
            
    $i = 1;
    $lis = '';
    
    if($startPageLabel)
    {
      $lis .= '<li class="rex-lvl'. $i .'"><a href="'. rex_getUrl($REX['START_ARTICLE_ID']) .'">'. htmlspecialchars($startPageLabel) .'</a></li>';
      $i++;

      // StartArticle nicht doppelt anzeigen
      if(isset($path[0]) && $path[0] == $REX['START_ARTICLE_ID'])
      {
        unset($path[0]);
      }
    }
    
    foreach($path as $pathItem)
    {
      $cat = OOCategory::getCategoryById($pathItem);
      $lis .= '<li class="rex-lvl'. $i .'"><a href="'. $cat->getUrl() .'">'. htmlspecialchars($cat->getName()) .'</a></li>';
      $i++;
    }
    
    if($includeCurrent)
    {
      if($art = OOArticle::getArticleById($this->current_article_id))
        if(!$art->isStartpage())
        {
          $lis .= '<li class="rex-lvl'. $i .'">'. htmlspecialchars($art->getName()) .'</li>';
        }else
        {
        	$cat = OOCategory::getCategoryById($this->current_article_id);
          $lis .= '<li class="rex-lvl'. $i .'">'. htmlspecialchars($cat->getName()) .'</li>';
        }
    }
    
    return '<ul class="rex-breadcrumb">'. $lis .'</ul>';
	}
	
	/**
	 * @see getBreadcrumb()
	 */
  /*public*/ function showBreadcrumb($includeCurrent = FALSE, $category_id = 0)
  {
    echo $this->getBreadcrumb($includeCurrent, $category_id);
  }
  
	/*public*/ function setClasses($classes)
	{
	  $this->classes = $classes;
	}

	/*public*/ function setLinkClasses($classes)
	{
	  $this->linkclasses = $classes;
	}

	/*private*/ function _setActivePath()
	{
		global $REX;

		$article_id = $REX["ARTICLE_ID"];
		if($OOArt = OOArticle::getArticleById($article_id))
		{
		  $path = trim($OOArt->getValue("path"), '|');
		  
		  $this->path = array();
		  if($path != "")
			 $this->path = explode("|",$path);
			 
      $this->current_article_id = $article_id;
			$this->current_category_id = $OOArt->getCategoryId();
			return TRUE;
		}
		
		return FALSE;
	}

	/*protected*/ function _getNavigation($category_id,$ignore_offlines = TRUE)
	{
	  static $depth = 0;
	  
    if($category_id < 1)
	  	$nav_obj = OOCategory::getRootCategories($ignore_offlines);
		else
	  	$nav_obj = OOCategory::getChildrenById($category_id, $ignore_offlines);

  	$return = "";

		if(count($nav_obj)>0)
		  $return .= '<ul class="rex-navi'. ($depth+1) .'">';

		foreach($nav_obj as $nav)
		{
		  $liClass = '';
		  $linkClass = '';
		  
		  // classes abhaengig vom pfad
			if($nav->getId() == $this->current_category_id)
			{
			  $liClass .= ' rex-current';
			  $linkClass .= ' rex-current';
			}
			elseif (in_array($nav->getId(),$this->path))
			{
        $liClass .= ' rex-active';
			  $linkClass .= ' rex-active';
			}
			else
			{
        $liClass .= ' rex-normal';
			}
			
      // classes abhaengig vom level
      if(isset($this->classes[$depth]))
        $liClass .= ' '. $this->classes[$depth];

      if(isset($this->linkclasses[$depth]))
        $linkClass .= ' '. $this->linkclasses[$depth];


      
			$liClass   = $liClass   == '' ? '' : ' class="'. ltrim($liClass) .'"';
			$linkClass = $linkClass == '' ? '' : ' class="'. ltrim($linkClass) .'"';
			  
      $return .= '<li class="rex-article-'. $nav->getId() .'"'. $liClass .'>';
			$return .= '<a'. $linkClass .' href="'.$nav->getUrl().'">'.htmlspecialchars($nav->getName()).'</a>';

			$depth++;
			if(($this->open || 
			    $nav->getId() == $this->current_category_id || 
			    in_array($nav->getId(),$this->path))
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

class rex_be_navigation
{
  
  // alt
  var $headlines = array();
  
  // neu
  var $pages;
  
  function addPage(/*rex_be_page*/ &$pageObj)
  {
    if(!isset($this->pages[$pageObj->getBlock()]))
      $this->pages[$pageObj->getBlock()] = array();
      
    $this->pages[$pageObj->getBlock()][] = $pageObj;
  }
  
  function getNavigation()
  {
    global $REX,$I18N;
    $s = '<dl class="rex-navi">';
    foreach($this->pages as $block => $blockPages)
    {
      $headline = $this->getHeadline($block);
      $s .= '<dt>'. $headline .'</dt><dd>';
      $s .= $this->_getNavigation($blockPages, 0, $block);
      $s .= '</dd>' . "\n";
    }
    $s .= '</dl>';
    return $s;
    
  }
  
  /*private*/ function _getNavigation($blockPages, $level = 0, $block = '')
  {
      $level++;
      $id = '';
      if($block != '')
        $id = ' id="rex-navi-'. $block .'"';
      $class = ' class="rex-navi-level-'. $level .'"';
      
      $echo = '<ul'. $id . $class .'>';
      $first = TRUE;
      foreach($blockPages as $pageObj)
      {
        if($first)
        {
          $first = FALSE;
          $pageObj->setItemAttr('class', $pageObj->getItemAttr('class'). ' rex-navi-first');
        }

        $pageObj->setLinkAttr('class', $pageObj->getItemAttr('class').' '. $pageObj->getLinkAttr('class'));
          
        $itemAttr = '';
        foreach($pageObj->getItemAttr(null) as $name => $value)
        {
          $itemAttr .= $name .'="'. trim($value) .'" ';
        }
        
        $linkAttr = '';
        foreach($pageObj->getLinkAttr(null) as $name => $value)
        {
          $linkAttr .= $name .'="'. trim($value) .'" ';
        }
        
        $href = str_replace('&', '&amp;', $pageObj->getHref());
    
            
        $echo .= '<li '. $itemAttr .'><a '. $linkAttr . ' href="'. $href .'">'. $pageObj->getTitle() .'</a>';
        $subpages = $pageObj->getSubPages();
        if(is_array($subpages) && count($subpages) > 0)
        {
          $echo .= $this->_getNavigation($subpages, $level);
        }
        $echo .= '</li>';
      }

      $echo .= '</ul>';
    
      return $echo;
  }
  
  function setActiveElements()
  {
    // echo '<pre>';var_dump($this->navi); echo '</pre>';
    foreach($this->pages as $block => $blockPages)
//    foreach($this->navi as $type => $p)
    {
      // echo "<br /><h1>$type</h1>";
      foreach($blockPages as $mn => $pageObj)
      {
        $condition = $pageObj->getActivateCondition();
        if($this->_getStatus($condition))
        {
          $pageObj->setLinkAttr('class', $pageObj->getLinkAttr('class').' rex-active');
        }

        $subpages =& $pageObj->getSubPages();
        foreach($subpages as $sn => $subpageObj)
        {
          $condition = $subpageObj->getActivateCondition();
          if($this->_getStatus($condition))
          {
            $subpageObj->setLinkAttr('class', $subpageObj->getLinkAttr('class').' rex-active');
          }
        }
      }
    }
  }
  
  function _getStatus($a)
  {
    if(empty($a))
    {
      return false;
    }
    
    foreach($a as $k => $v)
    {
      $v = (array)  $v;
      if(!in_array(rex_request($k), $v))
      {
        return FALSE;
      }
    }
    return TRUE;
  }
  
  function setHeadline($type, $headline)
  {
    $this->headlines[$type] = rex_translate($headline);
  }
  
  function getHeadline($type)
  {
    global $I18N;

    if (isset($this->headlines[$type]))
      return $this->headlines[$type];

    return $I18N->msg('navigation_'.$type);
  }
  
  function factory()
  {
    $r = new rex_be_navigation();
    return $r;
  }
  
  /*public static*/ function getSetupPage()
  {
    global $I18N;
      
    $page = new rex_be_page($I18N->msg('setup'), 'system');
    $page->setIsCorePage(true);
    return $page;
  }
  
  /*public static*/ function getLoginPage()
  {
    $page = new rex_be_page('login', 'system');
    $page->setIsCorePage(true);
    $page->setHasNavigation(false);
    return $page;
  }
  
  /*public static*/ function getLoggedInPages($rexUser)
  {
    global $I18N;
    
    $pages = array();
    
    $pages['profile'] = new rex_be_main_page($I18N->msg('profile'), 'system');
    $pages['profile']->setIsCorePage(true);
    
    $pages['credits'] = new rex_be_main_page($I18N->msg('credits'), 'system');
    $pages['credits']->setIsCorePage(true);
    
    if ($rexUser->isAdmin() || $rexUser->hasStructurePerm())
    {
      $pages['structure'] = new rex_be_main_page($I18N->msg('structure'), 'system', array('page' => 'structure'));
      $pages['structure']->setIsCorePage(true);
      
      if($rexUser->hasMediaPerm())
      {
        $pages['mediapool'] = new rex_be_popup_page($I18N->msg('mediapool'), 'system', 'openMediaPool()');
        $pages['mediapool']->setIsCorePage(true);
      }
      
      $pages['linkmap'] = new rex_be_popup_page($I18N->msg('linkmap'), 'system');
      $pages['linkmap']->setIsCorePage(true);
      
      $pages['content'] = new rex_be_main_page($I18N->msg('content'), 'system');
      $pages['content']->setIsCorePage(true);
      
    }elseif($rexUser->hasMediaPerm())
    {
      $pages['mediapool'] = new rex_be_popup_page($I18N->msg('mediapool'), 'system', 'openMediaPool()');
      $pages['mediapool']->setIsCorePage(true);
    }
    
    if ($rexUser->isAdmin())
    {
      $pages['template'] = new rex_be_main_page($I18N->msg('template'), 'system', array('page'=>'template'));
      $pages['template']->setIsCorePage(true);
      
      $modules = new rex_be_page($I18N->msg('modules'), array('page'=>'module', 'subpage' => ''));
      $modules->setIsCorePage(true);
      $modules->setHref('index.php?page=module&subpage=');
      
      $actions = new rex_be_page($I18N->msg('actions'), array('page'=>'module', 'subpage' => 'actions'));
      $actions->setIsCorePage(true);
      $actions->setHref('index.php?page=module&subpage=actions');
      
      $pages['module'] = new rex_be_main_page($I18N->msg('modules'), 'system', array('page'=>'module'));
      $pages['module']->setIsCorePage(true);
      $pages['module']->addSubPage($modules);
      $pages['module']->addSubPage($actions);
      
      $pages['user'] = new rex_be_main_page($I18N->msg('user'), 'system', array('page'=>'user'));
      $pages['user']->setIsCorePage(true);
      
      $pages['addon'] = new rex_be_main_page($I18N->msg('addon'), 'system', array('page'=>'addon'));
      $pages['addon']->setIsCorePage(true);

      $settings = new rex_be_page($I18N->msg('main_preferences'), array('page'=>'specials', 'subpage' => ''));
      $settings->setIsCorePage(true);
      $settings->setHref('index.php?page=specials&subpage=');
      
      $languages = new rex_be_page($I18N->msg('languages'), array('page'=>'specials', 'subpage' => 'lang'));
      $languages->setIsCorePage(true);
      $languages->setHref('index.php?page=specials&subpage=lang');
      
      $pages['specials'] = new rex_be_main_page($I18N->msg('addon'), 'system', array('page'=>'specials'));
      $pages['specials']->setIsCorePage(true);
      $pages['specials']->addSubPage($settings);
      $pages['specials']->addSubPage($languages);
    }
    
    return $pages;    
  }
}

class rex_be_page
{
  var $pageName;
  var $title;
  
  var $href;
  var $linkAttr;
  var $itemAttr;
  
  var $subPages;
  
  var $isCorePage;
  var $hasNavigation;
  var $activateCondition;
  
  function rex_be_page($title, $activateCondition = array())
  {
    $this->title = $title;
    $this->subPages = array();
    $this->itemAttr = array();
    $this->linkAttr = array();
    
    $this->setIsCorePage(false);
    $this->setHasNavigation(true);
    $this->activateCondition = $activateCondition;
  }
  
  function setPageName($pageName)
  {
    $this->pageName = $pageName;
  }
  
  function getPageName()
  {
    return $this->pageName;
  }
  
  function getItemAttr($name, $default = '')
  {
    // return all attributes if null is passed as name
    if($name === null)
    {
      return $this->itemAttr;
    }
    
    return isset($this->itemAttr[$name]) ? $this->itemAttr[$name] : $default;
  }
  
  function setItemAttr($name, $value)
  {
    $this->itemAttr[$name] = $value;
  }
  
  function getLinkAttr($name, $default = '')
  {
    // return all attributes if null is passed as name
    if($name === null)
    {
      return $this->linkAttr;
    }
    
    return isset($this->linkAttr[$name]) ? $this->linkAttr[$name] : $default;
  }
  
  function setLinkAttr($name, $value)
  {
    $this->linkAttr[$name] = $value;
  }
  
  function setHref($href)
  {
    $this->href = $href;
  }
  
  function getHref()
  {
    return $this->href;
  }
  
  function setIsCorePage($isCorePage)
  {
    $this->isCorePage = $isCorePage;
  }
  
  function setHasNavigation($hasNavigation)
  {
    $this->hasNavigation = $hasNavigation;
  }
  
  function addSubPage(/*rex_be_subpage*/ $subpage)
  {
    $this->subPages[] = $subpage;
  }
  
  function &getSubPages()
  {
    return $this->subPages;
  }
  
  function getTitle()
  {
    return $this->title;
  }
  
  function getActivateCondition()
  {
    return $this->activateCondition;
  }
  
  function isCorePage()
  {
    return $this->isCorePage;  
  }
  
  function hasNavigation()
  {
    return $this->hasNavigation;
  }
}
  
class rex_be_main_page extends rex_be_page
{
  var $block;
  
  function rex_be_main_page($title, $block, $activateCondition = array())
  {
    parent::rex_be_page($title, $activateCondition);
    $this->setBlock($block);
  }
  
  function setBlock($block)
  {
    $this->block = $block;
  }
  
  function getBlock()
  {
    return $this->block;
  }
  
  function _set($key, $value)
  {
    if(!is_string($key))
      return;
      
    $setter = array($this, 'set'. ucfirst($key));
    if(is_callable($setter))
    {
      call_user_func($setter, $value);
    }
  }
}

class rex_be_popup_page extends rex_be_main_page
{
  var $onclick;
  
  function rex_be_popup_page($title, $block, $onclick = '', $activateCondition = array())
  {
    parent::rex_be_main_page($title, $block, $activateCondition);
    
    $this->setHasNavigation(false);
    $this->onclick = $onclick;
    $this->setItemAttr('class', 'rex-popup');
    $this->setLinkAttr('class', 'rex-popup');
  }
}