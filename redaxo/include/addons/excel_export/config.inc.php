<?php
error_reporting( E_ALL^E_NOTICE);

/*
    excel_export Addon by <a href="mailto:staab@public-4u.de">Markus Staab</a>
    <a href="http://www.public-4u.de">www.public-4u.de</a>
    20.06.2005
    Version RC1
*/

if ( !defined( 'TBL_EXCEL_EXPORT')) {
    define( 'TBL_EXCEL_EXPORT', 'rex_11_excel_export');
}

if ( !defined( 'TBL_EXCEL_EXPORT_TBL')) {
    define( 'TBL_EXCEL_EXPORT_TBL', 'rex_11_excel_export_tbl');
}

$mypage = 'excel_export'; 				// only for this file

$REX['ADDON']['page'][$mypage] = $mypage;
$REX['ADDON']['name'][$mypage] = 'Excel Export';
$REX['ADDON']['perm'][$mypage] = 'excel_export[]';

$REX['PERM'][] = 'excel_export[]';
$REX['PERM'][] = 'excel_export[admin]';

?>