<?php

$mypage = "tinymce";

$REX['ADDON']['rxid'][$mypage] = "REX_REDAXO";
$REX['ADDON']['page'][$mypage] = $mypage;
$REX['ADDON']['name'][$mypage] = "TinyMCE";
$REX['ADDON']['lang'][$mypage] = "de";

$BaseDir = dirname(__FILE__);
include_once $BaseDir.'/tiny2.php';

?>