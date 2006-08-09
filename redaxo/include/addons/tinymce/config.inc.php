<?php

$mypage = "tinymce";

$REX['ADDON']['rxid'][$mypage] = "REX_REDAXO";
$REX['ADDON']['page'][$mypage] = $mypage;
$REX['ADDON']['name'][$mypage] = "TinyMCE";
$REX['ADDON']['lang'][$mypage] = "de";


// Include tinylib
if($REX['REDAXO']){
	include_once $REX['INCLUDE_PATH'].'/addons/tinymce/classes/class.tiny.inc.php';
}
?>