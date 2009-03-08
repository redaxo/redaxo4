<?php

/**
 * Layout Kopf des Backends
 * @package redaxo4
 * @version svn:$Id$
 */

$page_title = $REX['SERVERNAME'];

if(!isset($page_name))
  $page_name = $REX["PAGES"][strtoupper($page)][0];
  
if ($page_name != '')
  $page_title .= ' - ' . $page_name;

$body_id = str_replace('_', '-', $page);
$bodyAttr = 'id="rex-page-'. $body_id .'"';

if ($REX["PAGE_NO_NAVI"]) $bodyAttr .= ' onunload="closeAll();"';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $I18N->msg('htmllang'); ?>" lang="<?php echo $I18N->msg('htmllang'); ?>">
<head>
  <title><?php echo htmlspecialchars($page_title) ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $I18N->msg('htmlcharset'); ?>" />
  <meta http-equiv="Content-Language" content="<?php echo $I18N->msg('htmllang'); ?>" />
  <link rel="stylesheet" type="text/css" href="media/css_import.css" media="screen, projection, print" />
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

if ($REX['USER'] && !$REX["PAGE_NO_NAVI"])
{
  
  $navi_system = array();
  $navi_addons = array();
  foreach($REX['USER']->pages as $pageKey => $pageArr)
  {
    $pageKey = strtolower($pageKey);
    if(!in_array($pageKey, array("credits","profile","content","linkmap")))
    {
      $item = array();
      
      $item['page'] = $pageKey;
      $item['id'] = 'rex-navi-page-'.$pageKey;
      $item['class'] = '';
      if($pageKey == $REX["PAGE"]) 
        $item['class'] = 'rex-active';

      if($pageArr[1] != 1)
      {
        // ***** Basis
        if($pageKey == "mediapool")
        {
          $item['href'] = '#';
          $item['onclick'] = 'openMediaPool();';
        }
        else
        { 
          $item['href'] = 'index.php?page='.$pageKey;
        }
        
        $item['extra'] = rex_accesskey($pageArr[0], $accesskey++);
        $item['tabindex'] = rex_tabindex(false);
        $navi_system[$pageArr[0]] = $item;
      }
      else
      {
        // ***** AddOn
        if(isset ($REX['ADDON']['link'][$pageKey]) && $REX['ADDON']['link'][$pageKey] != "") 
          $item['href'] = $REX['ADDON']['link'][$pageKey];
        else 
          $item['href'] = 'index.php?page='.$pageKey;
          
        if(isset ($REX['ACKEY']['ADDON'][$pageKey]))
          $item['extra'] = rex_accesskey($name, $REX['ACKEY']['ADDON'][$pageKey]);
        else 
          $item['extra'] = rex_accesskey($pageArr[0], $accesskey++);
        
        $item['tabindex'] = rex_tabindex(false);
        $navi_addons[$pageArr[0]] = $item;
      }
    }
  }
  
  echo '<dl class="rex-navi">';
  
  foreach(array('system' => $navi_system, 'addon' => $navi_addons) as $topic => $naviList)
  {
    if(count($naviList) == 0)
      continue;
      
    $headline = $topic == 'system' ? $I18N->msg('navigation_basis') : $I18N->msg('navigation_addons');
    
    echo '<dt>'. $headline .'</dt><dd>';
    echo '<ul id="rex-navi-'. $topic .'">';
    
    $first = TRUE;
    foreach($naviList as $pageTitle => $item)
    {
      if($first)
        $item['class'] .= ' rex-navi-first';
        
      $class = $item['class'] != '' ? ' class="'. $item['class'] .'"' : '';
      unset($item['class']);
      $extra = $item['extra'];
      unset($item['extra']);
      $id = $item['id'];
      unset($item['id']);
      $p = $item['page'];
      unset($item['page']);
      
      $tags = '';
      foreach($item as $tag => $value)
        $tags .= ' '. $tag .'="'. $value .'"';
      
      echo '<li'. $class .' id="'. $id .'"><a'. $class . $tags . $extra .'>'. $pageTitle .'</a>';

			// ***** Subnavi
      if(
      	isset($REX['SUBPAGES'][$p]) && 
      	is_array($REX['SUBPAGES'][$p]) && 
      	count($REX['SUBPAGES'][$p])>0)
      {
      	echo '<ul>';
	      $subfirst = TRUE;
	      $subpage = rex_request("subpage","string");
	      foreach($REX['SUBPAGES'][$p] as $sb)
	      {
	      	$class = '';
        	$id = 'rex-navi-'.$p.'-subpage-'.$sb[0];
	      	if($subfirst)
        		$class .= ' rex-subnavi-first';
        	if($p == $REX["PAGE"] && $subpage == $sb[0]) 
		        $class .= ' rex-active';
     			$class = $class != '' ? ' class="'. $class .'"' : '';
     			$subitem = array();
     			$subitem['href'] = 'index.php?page='.$p.'&amp;subpage='.$sb[0];
     			$tags = '';
    		  foreach($subitem as $tag => $value)
		        $tags .= ' '. $tag .'="'. $value .'"';
	        echo '<li'. $class .' id="'. $id .'"><a'. $class . $tags . $extra .'>> '. $sb[1] .'</a></li>';
		      $subfirst = FALSE;
	      }
	      echo '</ul>';
      }
      // ***** Subnavi
      
      echo '</li>';
      $first = false;
    }
    echo '</ul></dd>' . "\n";
  }
  echo '</dl>';
}

?>
</div>


<div id="rex-wrapper">
<div id="rex-wrapper2">