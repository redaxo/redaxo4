<?php

include $REX["INCLUDE_PATH"]."/layout/top.php";
echo '<div id="rex-addon-output">';

$subpages = array();
rex_title("be_style", $subpages);

$addon = "be_style";

foreach(rex_read_plugins_folder($addon) as $plugin)
{
	if (OOPlugin::isInstalled($addon, $plugin))
	{

	}
	if (OOPlugin::isActivated($addon, $plugin))
	{

	}  
	echo '<br />'.$plugin;
}

echo '</div>';

include $REX["INCLUDE_PATH"]."/layout/bottom.php";
