<?php

// error_reporting(E_ALL ^ E_NOTICE);
$page = 'community';

include $REX["INCLUDE_PATH"]."/layout/top.php";
echo '<div id="rex-addon-output">';

include $REX["INCLUDE_PATH"]."/addons/xform/classes/basic/class.rexform.inc.php";
include $REX["INCLUDE_PATH"]."/addons/xform/classes/basic/class.rexlist.inc.php";
include $REX["INCLUDE_PATH"]."/addons/xform/classes/basic/class.rexselect.inc.php";
include $REX["INCLUDE_PATH"]."/addons/xform/classes/basic/class.rexradio.inc.php";

$subpages = array();
$subpages[] = array( '' , '&Uuml;bersicht');
if ($REX['USER']->isValueOf("rights","admin[]") || $REX['USER']->isValueOf("rights","community[users]")) $subpages[] = array ('user' , 'User Verwaltung');
if ($REX['USER']->isValueOf("rights","admin[]") || $REX['USER']->isValueOf("rights","community[admin]")) $subpages[] = array ('user_fields' , 'User Felder erweitern');
if ($REX['USER']->isValueOf("rights","admin[]") || $REX['USER']->isValueOf("rights","community[admin]")) $subpages[] = array ('plugin_manager' , 'PlugIns');

// PlugIn Seiten einbauen..
foreach($REX['ADDON']['community']['subpages'] as $subpage)
{
	if ($REX['USER']->isValueOf("rights","admin[]") ||
	    $REX['USER']->isValueOf("rights","community[admin]") ||
	    $REX['USER']->isValueOf("rights","community[". $subpage ."]"))
  {
	  $subpages[] = $subpage;
  }
}







$back_to_overview = '<table cellpadding=5 class="rex-table"><tr><td><a href="index.php?page='.$page.'&subpage='.$subpage.'"><b>&laquo; Zurück zur Übersicht</b></a></td></tr></table><br />';

rex_title("Community", $subpages);

$subpage = rex_request("subpage","string","");

function deep_in_array($value, $array, $case_insensitive = false){
   foreach($array as $item){
       if(is_array($item)) $ret = deep_in_array($value, $item, $case_insensitive);
       else $ret = ($case_insensitive) ? strtolower($item)==$value : $item==$value;
       if($ret)return $ret;
   }
   return false;
}

if (!deep_in_array($subpage,$subpages)) $subpage = "";

if ($subpage != "")
{
	if (substr($subpage,0,7)=="plugin.")
	{
		include $REX["INCLUDE_PATH"].'/addons/'.$page.'/plugins/'.substr($subpage,7,strlen($subpage)-7).'/pages/index.inc.php';
	}else
	{
		include $REX["INCLUDE_PATH"].'/addons/'.$page.'/pages/'.$subpage.'.inc.php';
	}
}else
{
	echo '<table class="rex-table">';
	echo '<tr><th>Community - Übersicht</th></tr>';
	foreach($subpages as $sp)
	{
		echo '<tr><td>&raquo; <a href="index.php?page='.$page.'&subpage='.$sp[0].'">'.$sp[1].'</a></td></tr>';
	}
	echo '</table>';
}

echo '</div>';

include $REX["INCLUDE_PATH"]."/layout/bottom.php";

?>