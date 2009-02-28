<?php

/**
 * Backendstyle Addon
 * 
 * @author jan.kristinus[at]redaxo[dot]de Jan Kristinus
 * @author <a href="http://www.yakamara.de">www.yakamara.de</a>
 * 
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 * @author <a href="http://www.redaxo.de">www.redaxo.de</a>
 *
 * @package redaxo4
 * @version $Id: config.inc.php,v 1.13 2008/03/26 21:06:37 kills Exp $
 */

$mypage = 'be_style';

/* Addon Parameter */
$REX['ADDON']['rxid'][$mypage] = '467';
$REX['ADDON']['page'][$mypage] = $mypage;
//$REX['ADDON']['perm'][$mypage] = 'be_style[]';
$REX['ADDON']['version'][$mypage] = '1.2';
$REX['ADDON']['author'][$mypage] = 'Jan Kristinus, Markus Staab';
$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';

if($REX["REDAXO"])
{
	rex_register_extension('PAGE_HEADER', 'rex_be_style_css_add');
	function rex_be_style_css_add($params)
	{
		$addon = "be_style";
		foreach(OOPlugin::getAvailablePlugins($addon) as $plugin)
		{
			echo "\n".'<link rel="stylesheet" type="text/css" href="../files/addons/'.$addon.'/plugins/'.$plugin.'/css_main.css" media="screen, projection, print" />';
		}
	}
	
	// Menupunkt nur einbinden, falls ein Plugin sich angemeldet hat
	// via BE_STYLE_PAGE_CONTENT inhalt auszugeben 
  rex_register_extension('ADDONS_INCLUDED', 'rex_be_add_page');
  function rex_be_add_page($params)
  {
    if(rex_extension_is_registered('BE_STYLE_PAGE_CONTENT'))
    {
      global $REX;
      
      $mypage = 'be_style';
      $REX['ADDON']['name'][$mypage] = 'Backend Style';
    }
  }
}
