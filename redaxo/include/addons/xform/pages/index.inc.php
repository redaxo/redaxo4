<?php

$page = 'xform';

$I18N_XFORM = new i18n($REX['LANG'], $REX['INCLUDE_PATH'].'/addons/'.$mypage.'/lang/');

include $REX["INCLUDE_PATH"]."/layout/top.php";
echo '<div id="rex-addon-output">';

$subpage = rex_request("subpage","string");

$subpages = array();
$subpages[] = array( '' , $I18N_XFORM->msg("overview"));
if ($REX['USER']->isAdmin() || $REX['USER']->isValueOf("rights","xform[]") || $REX['USER']->isValueOf("rights","xform_email[]")) 
	$subpages[] = array ('email_templates' , $I18N_XFORM->msg("email_templates"));
if ($REX['USER']->isAdmin() || $REX['USER']->isValueOf("rights","xform[]")) 
	$subpages[] = array ('description' , $I18N_XFORM->msg("description"));
if ($REX['USER']->isAdmin() || $REX['USER']->isValueOf("rights","xform[]")) 
	$subpages[] = array ('module' , $I18N_XFORM->msg("install_module"));

rex_title("XForm", $subpages);

function deep_in_array($value, $array, $case_insensitive = false)
{
   foreach($array as $item){ if(is_array($item)) $ret = deep_in_array($value, $item, $case_insensitive); else $ret = ($case_insensitive) ? strtolower($item)==$value : $item==$value; if($ret)return $ret; }
   return false;
}

if (!deep_in_array($subpage,$subpages)) 
	$subpage = "help";

if ($subpage != "")
{
	echo '<div class="rex-addon-output">';
	include $REX["INCLUDE_PATH"]."/addons/$page/pages/$subpage.inc.php";
	echo '</div>';
}else
{
	echo '<div class="rex-addon-output">';
	echo '<h2 class="rex-hl2">XFORM - '.$I18N_XFORM->msg("overview").'</h2>';
	
	echo '<div class="rex-addon-content"><ul>';
	foreach($subpages as $sp)
	{
		echo '<li><a href="index.php?page='.$page.'&amp;subpage='.$sp[0].'">'.$sp[1].'</a></li>';
	}
	echo '</ul></div>';
	echo '</div>';
}

echo '</div>';

include $REX["INCLUDE_PATH"]."/layout/bottom.php";

?>