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
$REX['ADDON']['name'][$mypage] = 'Backend Style';
//$REX['ADDON']['perm'][$mypage] = 'be_style[]';
$REX['ADDON']['version'][$mypage] = '1.0';
$REX['ADDON']['author'][$mypage] = 'Jan Kristinus, Markus Staab';
$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';

if($REX["REDAXO"])
{
	rex_register_extension('PAGE_HEADER', 'rex_be_style_css_add');
	function rex_be_style_css_add($params)
	{
		$addon = "be_style";
		foreach(rex_read_plugins_folder($addon) as $plugin)
		{
			if(OOPlugin::isActivated($addon, $plugin) && OOPlugin::isInstalled($addon, $plugin))
				echo "\n".'<link rel="stylesheet" type="text/css" href="files/addons/'.$addon.'/plugins/'.$plugin.'/main.css" media="screen, projection, print" />';
		}
	}
}
