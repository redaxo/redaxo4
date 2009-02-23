<?php

$page = 'xform';

include $REX["INCLUDE_PATH"]."/layout/top.php";
echo '<div id="rex-addon-output">';

include $REX["INCLUDE_PATH"]."/addons/xform/classes/basic/class.rexform.inc.php";
include $REX["INCLUDE_PATH"]."/addons/xform/classes/basic/class.rexlist.inc.php";
include $REX["INCLUDE_PATH"]."/addons/xform/classes/basic/class.rexselect.inc.php";
include $REX["INCLUDE_PATH"]."/addons/xform/classes/basic/class.rexradio.inc.php";

$subpages = array();
$subpages[] = array( '' , 'Übersicht');
if ($REX['USER']->isValueOf("rights","admin[]") || $REX['USER']->isValueOf("rights","xform[]") || $REX['USER']->isValueOf("rights","xform_email[]")) $subpages[] = array ('email_templates' , 'E-Mail Templates');
if ($REX['USER']->isValueOf("rights","admin[]") || $REX['USER']->isValueOf("rights","xform[]")) $subpages[] = array ('description' , 'Beschreibung/Beispiele');
if ($REX['USER']->isValueOf("rights","admin[]") || $REX['USER']->isValueOf("rights","xform[]")) $subpages[] = array ('module' , 'Modul');

$back_to_overview = '<table cellpadding=5 class="rex-table"><tr><td><a href="index.php?page='.$page.'&subpage='.$subpage.'"><b>&laquo; Zurück zur Übersicht</b></a></td></tr></table><br />';

rex_title("XForm", $subpages);

$subpage = "";
if (isset($_REQUEST["subpage"]) && $_REQUEST["subpage"] != "") $subpage = $_REQUEST["subpage"];

function deep_in_array($value, $array, $case_insensitive = false){
   foreach($array as $item){
       if(is_array($item)) $ret = deep_in_array($value, $item, $case_insensitive);
       else $ret = ($case_insensitive) ? strtolower($item)==$value : $item==$value;
       if($ret)return $ret;
   }
   return false;
}

if (!deep_in_array($subpage,$subpages)) $subpage = "help";

if ($subpage != "")
{
	include $REX["INCLUDE_PATH"]."/addons/$page/pages/$subpage.inc.php";
}else
{
	echo '<div class="rex-addon-output">';
	echo '<h2 class="rex-hl2">XFORM - &Uuml;bersicht</h2>';
	
	echo '<div class="rex-addon-content"><ul>';
	foreach($subpages as $sp)
	{
		echo '<li><a href="index.php?page='.$page.'&subpage='.$sp[0].'">'.$sp[1].'</a></li>';
	}
	echo '</ul></div>';
	echo '</div>';
}

echo '</div>';

include $REX["INCLUDE_PATH"]."/layout/bottom.php";

?>