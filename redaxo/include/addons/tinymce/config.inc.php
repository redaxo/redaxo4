<?php

$mypage = "tinymce";

$REX['ADDON']['rxid'][$mypage] = "52";
$REX['ADDON']['page'][$mypage] = $mypage;
$REX['ADDON']['name'][$mypage] = "TinyMCE";
$REX['ADDON']['perm'][$mypage] = "tiny_mce[]";

// Include tinylib
if($REX['REDAXO'])
{
	include_once $REX['INCLUDE_PATH'].'/addons/tinymce/classes/class.tiny.inc.php';
}
?>