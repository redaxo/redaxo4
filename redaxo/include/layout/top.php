<?php

/**
 * Layout Kopf des Backends
 * @package redaxo4
 * @version svn:$Id$
 */
 
$popups_arr = array('linkmap', 'mediapool');

$page_title = $REX['SERVERNAME'];

if(!isset($page_name))
  $page_name = $REX["PAGES"][strtolower($REX["PAGE"])]['title'];
  
if ($page_name != '')
  $page_title .= ' - ' . $page_name;

$body_id = str_replace('_', '-', $REX["PAGE"]);
$bodyAttr = 'id="rex-page-'. $body_id .'"';

if (in_array($body_id, $popups_arr))
  $bodyAttr .= ' class="rex-popup"';

if ($REX["PAGE_NO_NAVI"]) $bodyAttr .= ' onunload="closeAll();"';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $I18N->msg('htmllang'); ?>" lang="<?php echo $I18N->msg('htmllang'); ?>">
<head>
  <title><?php echo htmlspecialchars($page_title) ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $I18N->msg('htmlcharset'); ?>" />
  <meta http-equiv="Content-Language" content="<?php echo $I18N->msg('htmllang'); ?>" />
  <link rel="stylesheet" type="text/css" href="media/css_import.css" media="screen, projection, print" />
  <!--[if lte IE 7]>
		<link rel="stylesheet" href="media/css_ie_lte_7.css" type="text/css" media="screen, projection, print" />
	<![endif]-->
			
	<!--[if IE 7]>
		<link rel="stylesheet" href="media/css_ie_7.css" type="text/css" media="screen, projection, print" />
	<![endif]-->
	
	<!--[if lte IE 6]>
		<link rel="stylesheet" href="media/css_ie_lte_6.css" type="text/css" media="screen, projection, print" />
	<![endif]-->

  <!-- jQuery immer nach den Stylesheets! -->
  <script src="media/jquery.min.js" type="text/javascript"></script>
  <script src="media/standard.js" type="text/javascript"></script>
  <script type="text/javascript">
  <!--
  var redaxo = true;

  // jQuery is now removed from the $ namespace
  // to use the $ shorthand, use (function($){ ... })(jQuery);
  // and for the onload handler: jQuery(function($){ ... });
  jQuery.noConflict();
  //-->
  </script>
<?php
  // ----- EXTENSION POINT
  echo rex_register_extension_point('PAGE_HEADER', '');
?>
</head>
<body <?php echo $bodyAttr; ?> onunload="closeAll();">
<div id="rex-website">
<div id="rex-header">

  <p class="rex-header-top"><a href="../index.php" onclick="window.open(this.href);"><?php echo htmlspecialchars($REX['SERVERNAME']); ?></a></p>

</div>

<div id="rex-navi-logout"><?php
  
if ($REX['USER'] && !$REX["PAGE_NO_NAVI"])
{
  $accesskey = 1;
  $user_name = $REX['USER']->getValue('name') != '' ? $REX['USER']->getValue('name') : $REX['USER']->getValue('login');
  echo '<ul class="rex-logout"><li class="rex-navi-first"><span>' . $I18N->msg('logged_in_as') . ' '. htmlspecialchars($user_name) .'</span></li><li><a href="index.php?page=profile">' . $I18N->msg('profile_title') . '</a></li><li><a href="index.php?rex_logout=1"'. rex_accesskey($I18N->msg('logout'), $REX['ACKEY']['LOGOUT']) .'>' . $I18N->msg('logout') . '</a></li></ul>' . "\n";
}else if(!$REX["PAGE_NO_NAVI"])
{
  echo '<p class="rex-logout">' . $I18N->msg('logged_out') . '</p>';
}else
{
  echo '<p class="rex-logout">&nbsp;</p>';
}
  
?></div>

  <div id="rex-navi-main">
<?php

class rex_be_navigation
{
	
	var $navi = array();
	var $extras = array('onclick', 'onmouseover', 'title', 'href');
	
	function addElement($type, $params = array())
	{
		if(!isset($this->navi[$type]))
		  $this->navi[$type] = array();
		$this->navi[$type][] = $params;
	}

	function getNavigation()
	{
		global $REX,$I18N;
		$echo = '<dl class="rex-navi">';
		foreach($this->navi as $type => $m)
		{
			$headline = $I18N->msg('navigation_'.$type);
	    $echo .= '<dt>'. $headline .'</dt><dd>';
	    $echo .= $this->_getNavigation($m, 0, $type);
			$echo .= '</dd>' . "\n";
		}
  	$echo .= '</dl>';
		return $echo;
		
	}
	
	/*private*/ function _getNavigation($m, $level = 0, $type = "")
	{
			$level++;
			if($type != "")
				$id = 'rex-navi-'. $type;
			else
			  $id = 'rex-navi-level-'.$level;
			
	    $echo = '<ul id="'.$id.'">';
			$first = TRUE;
	    foreach($m as $item)
			{
				// echo '<pre>'; var_dump($item); echo '</pre>';
				if(!isset($item['class']))
				  $item['class'] = "";
        if($first)
          $item['class'] .= ' rex-navi-first';
      	$first = FALSE;
        if(isset($item['active']) && $item['active'])
      	  $item['class'] .= ' rex-active';
      	$item['class'] = $item['class'] != '' ? ' class="'.$item['class'].'"' : '';

      	if(!isset($item['extra']))
				  $item['extra'] = "";
        
				if(isset($item['id']))
				  $item['id'] = 'id="'.$item['id'].'"';
				else
				  $item['id'] = '';
	  
      	$tags = '';
      	foreach($item as $tag => $value)
        	if(in_array($tag,$this->extras))
        	  $tags .= ' '. $tag .'="'. $value .'"';
      
        	  // echo "<br />**".$tags;
        	  
        $echo .= '<li'. $item['class'] .' '. $item['id'] .'><a'. $item['class'] . $tags . $item['extra'] .'>'. @$item['title'] .'</a>';
        if(isset($item['subpages']) && is_array($item['subpages']))
        {
  	      $echo .= $this->_getNavigation($item['subpages'], $level);
        }
        $echo .= '</li>';
			}

			$echo .= '</ul>';
		
			return $echo;
	}
	
	function setActiveElements()
	{
		// echo '<pre>';var_dump($this->navi); echo '</pre>';
	  foreach($this->navi as $type => $p)
		{
			// echo "<br /><h1>$type</h1>";
			foreach($p as $mn => $item)
			{
				if(isset($item["active_when"]))
				{
					$this->navi[$type][$mn]["active"] = $this->_getStatus($item["active_when"]);
				}

				// echo "<br />$mn - ".$item["title"];
				if(isset($item["subpages"]))
			  {
			  	foreach($item["subpages"] as $sn => $sitem)
					{
						// echo "<br />".$sn." ".$sitem["title"];
					  if(isset($sitem["active_when"]))
					  {
					  	// echo '<pre>';var_dump($sitem["active_when"]); echo '</pre>';
					  	$this->navi[$type][$mn]["subpages"][$sn]["active"] = $this->_getStatus($sitem["active_when"]);
					  }
					}
			  }
			}
		  // echo "<hr/>";
		}
	}
	
	function _getStatus($a)
	{
		foreach($a as $k => $v)
		{
			if(rex_request($k) != $v)
			{
				return FALSE;
			}
		}
		return TRUE;
	}
	
	
	function factory()
	{
		$r = new rex_be_navigation();
		return $r;
	}
	
}







if ($REX['USER'] && !$REX["PAGE_NO_NAVI"])
{

	$n = rex_be_navigation::factory();
	
	foreach($REX['USER']->pages as $p => $pageArr)
  {
		$p = strtolower($p);
    if(!in_array($p, array("credits","profile","content","linkmap")))
    {
      $item = $pageArr;
      $item['page'] = $p;
      $item['id'] = 'rex-navi-page-'.$p;
      if(!isset($item['type']))
        $item['type'] = 'addons';
      if(!isset($item['href']))
        $item['href'] = 'index.php?page='.$p;
      /*
       if(isset ($REX['ACKEY']['ADDON'][$page]))
        $item['extra'] = rex_accesskey($name, $REX['ACKEY']['ADDON'][$page]);
      else 
        $item['extra'] = rex_accesskey($pageArr['title'], $accesskey++);
      */
        
      $item['tabindex'] = rex_tabindex(false);
      $n->addElement($item['type'], $item);
    }
  }
	
  // ----- EXTENSION POINT
  $n = rex_register_extension_point( 'NAVI_PREPARED', $n);
  $n->setActiveElements();
  echo $n->getNavigation();
	
	
	
	
	
	
	
	
	
      // ***** Subnavi
      /*
      $subpages = array();
      if(isset($item['subpages']))
        $subpages = $item['subpages'];
      unset($item['subpages']);
      
      if(count($subpages)>0)
      {
      	
      	echo '<ul class="rex-navi-level-2">';
	      $subfirst = TRUE;
	      $subpage = rex_request("subpage","string");
	      foreach($subpages as $sp)
	      {
	      	$class = '';
        	$id = 'rex-navi-'.$p.'-subpage-'.$sp[0];
	      	if($subfirst)
        		$class .= ' rex-navi-first';
        	if($p == $REX["PAGE"] && $subpage == $sp[0]) 
		        $class .= ' rex-active';
     			$class = $class != '' ? ' class="'. $class .'"' : '';
     			$subitem = array();
     			$subitem['href'] = 'index.php?page='.$p.'&amp;subpage='.$sp[0];
     			$tags = '';
    		  foreach($subitem as $tag => $value)
		        $tags .= ' '. $tag .'="'. $value .'"';
	        echo '<li'. $class .' id="'. $id .'"><a'. $class . $tags . $extra .'>'. $sp[1] .'</a></li>';
		      $subfirst = FALSE;
	      }
	      echo '</ul>';
      }
      */
      // ***** Subnavi
      
}

?>
</div>


<div id="rex-wrapper">
<div id="rex-wrapper2">