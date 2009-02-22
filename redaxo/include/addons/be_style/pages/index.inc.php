<?php

include $REX["INCLUDE_PATH"]."/layout/top.php";

$subpages = array();
rex_title("be_style", $subpages);

echo '<div class="rex-addon-output">';

echo '<h2 class="rex-hl2">Themes/Plugins</h2>';

echo '<div class="rex-addon-content">';


$addon = "be_style";
foreach(rex_read_plugins_folder($addon) as $plugin)
{
	if (OOPlugin::isInstalled($addon, $plugin))
	{

	}
	if (OOPlugin::isActivated($addon, $plugin))
	{

	}  
	echo $plugin.'<br />';
}
echo '</div>';



echo '</div>';

include $REX["INCLUDE_PATH"]."/layout/bottom.php";
