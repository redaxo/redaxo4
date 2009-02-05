<?php

/**
 * Layout Kopf des Backends
 * @package redaxo4
 * @version $Id: top.php,v 1.7 2008/04/02 19:58:00 kills Exp $
 */

if (!isset ($page_name))
  $page_name = '';

$page_title = $REX['SERVERNAME'];

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
  <meta http-equiv="Cache-Control" content="no-cache" />
  <meta http-equiv="Pragma" content="no-cache" />
  <link rel="stylesheet" type="text/css" href="media/css_v0_import.css" media="screen, projection, print" />
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
<body <?php echo $bodyAttr; ?>>
<div id="rex-website">
<div id="rex-header">

  <p class="rex-header-top"><a href="../index.php" onclick="window.open(this.href);"><?php echo htmlspecialchars($REX['SERVERNAME']); ?></a></p>

  <div id="rex-navi-header">
<?php

if (isset ($LOGIN) && $LOGIN && !$REX["PAGE_NO_NAVI"])
{
  $accesskey = 1;

  $user_name = $REX_USER->getValue('name') != '' ? $REX_USER->getValue('name') : $REX_USER->getValue('login');
  echo '<p class="rex-logout">' . $I18N->msg('name') . ' : <strong><a href="index.php?page=profile">' . htmlspecialchars($user_name) . '</a></strong> [<a href="index.php?rex_logout=1"'. rex_accesskey($I18N->msg('logout'), $REX['ACKEY']['LOGOUT']) .'>' . $I18N->msg('logout') . '</a>]</p>' . "\n";
  
  $navi_system = array();
  $navi_addons = array();

	$pages_no_display = array("CREDITS","PROFILE","CONTENT","LINKMAP");  

	$first_navi_basis = TRUE;
	$first_navi_addons = TRUE;
  foreach($REX_USER->pages as $k => $p)
  {
  	if(!in_array($k,$pages_no_display))
  	{
  		$link = '';
	  	$class = "";
	  	if($k == $REX["PAGE"]) 
	  		$class .= 'rex-active';

			if($p[1] != 1)
			{
				// ***** Basis
				if($first_navi_basis) 
					$class .= ' rex-navi-first';
				if($class != '')
					$class = ' class="'.$class.'"';
		  	$link .= '<li'.$class.' id="rex-navi-page-'.strtolower($k).'">';
				$link .= '<a ';
				if($k == "MEDIAPOOL") 
					$link .= 'href="#" onclick="openMediaPool();"';
				else 
					$link .= 'href="index.php?page='.$k.'"';
		  	$link .= rex_tabindex();
		  	$link .= rex_accesskey($p[0], $accesskey++);
	      $link .= '>'.$p[0].'</a>';
		  	$link .= '</li>';
		  	$first_navi_basis = FALSE;
			}
			else
			{
				// ***** AddOn
				if($first_navi_addons) 
					$class .= ' rex-navi-first';
				if($class != '')
					$class = ' class="'.$class.'"';
		  	$link .= '<li'.$class.' id="rex-navi-page-'.strtolower($k).'">';
		  	$link .= '<a ';
	  		if(isset ($REX['ADDON']['link'][$k]) && $REX['ADDON']['link'][$k] != "") 
	  			$link .= 'href="'.$link.'"';
				else 
					$link .= 'href="index.php?page='.$k.'"';
		  	$link .= rex_tabindex();
	      if(isset ($REX['ACKEY']['ADDON'][$k]))
	        $link .= rex_accesskey($name, $REX['ACKEY']['ADDON'][$k]);
	      else 
			  	$link .= rex_accesskey($p[0], $accesskey++);
	      $link .= '>'.$p[0].'</a>';
	      
    		$link .= '</li>';
		  	$first_navi_addons = FALSE;
			}
	  	$p[3] = $link;
	  	// Addon ?
	  	if($p[1]==1) $navi_addons[] = $p;
	  	else $navi_system[] = $p;
  	}  	
  }
  
	if(count($navi_system)>0)
	{
		echo '<h1>'.$I18N->msg('navigation_basis').'</h1>';
	  echo '<ul id="rex-navi-system">';
		foreach($navi_system as $p)
		{
			echo $p[3];
		}
	  echo '</ul>' . "\n";
	}

	if(count($navi_addons)>0)
	{
		echo '<h1>'.$I18N->msg('navigation_addons').'</h1>';
	  echo '<ul id="rex-navi-addon">';
		foreach($navi_addons as $p)
		{
			echo $p[3];
		}
	  echo '</ul>' . "\n";
	}

}else if(!$REX["PAGE_NO_NAVI"])
{
  echo '<p class="rex-logout">' . $I18N->msg('logged_out') . '</p>';
}else
{
  echo '<p class="rex-logout">&nbsp;</p>';
}

?>
  </div>

</div>

<div id="rex-wrapper">