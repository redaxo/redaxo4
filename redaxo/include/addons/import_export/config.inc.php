<?php

$mypage = 'import_export';        // only for this file

if ($REX['REDAXO'])
	$I18N->appendFile($REX['INCLUDE_PATH'].'/addons/'.$mypage.'/lang/');

$REX['ADDON']['rxid'][$mypage] = '1';     // unique id /
$REX['ADDON']['page'][$mypage] = $mypage;     // pagename/foldername
$REX['ADDON']['name'][$mypage] = 'Import/Export';   // name
$REX['ADDON']['perm'][$mypage] = 'import[]';    // permission
$REX['ADDON']['version'][$mypage] = "1.2";
$REX['ADDON']['author'][$mypage] = "Jan Kristinus";
$REX['ADDON']['supportpage'][$mypage] = 'forum.redaxo.de';

$REX['PERM'][] = 'import[]';