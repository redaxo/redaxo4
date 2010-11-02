<?php

$page = new rex_xform_manager();
$page->setType('com');
$page->setFilterTable('rex_com_group');
$page->setLinkVars(	array('page'=>'community','subpage'=>'plugin.group')	);
echo $page->getDataPage();











/*



include $REX["INCLUDE_PATH"]."/addons/xform/manage/functions/functions.inc.php";

$tables = array();
$tables[1] = array();
$tables[1]["name"] = $I18N->msg("com_tablefield");
$tables[1]["description"] = "desc group";
$tables[1]["label"] = "group";
$tables[1]["tablename"] = $REX['TABLE_PREFIX'].'com_group';
$tables[1]["tablefield"] = $REX['TABLE_PREFIX'].'com_field';
$tables[1]["search"] = TRUE;
$tables[1]["list_amount"] = 50;

echo '<pre>';
var_dump($tables);
echo '</pre>';


echo '<hr/>';


$tables = rex_xform_manage_getTables($REX['TABLE_PREFIX'].'com_table');

echo '<pre>';
var_dump($tables);
echo '</pre>';

exit;



*/

// ---------- feste felder
/*
$REX["ADDON"]["community"]["ff"] = array();
$REX["ADDON"]["community"]["ff"][] = "id";
$REX["ADDON"]["community"]["ff"][] = "login";
$REX["ADDON"]["community"]["ff"][] = "password";
$REX["ADDON"]["community"]["ff"][] = "email";
$REX["ADDON"]["community"]["ff"][] = "status";
$REX["ADDON"]["community"]["ff"][] = "name";
$REX["ADDON"]["community"]["ff"][] = "firstname";
$REX["ADDON"]["community"]["ff"][] = "activation_key";
*/

// include $REX["INCLUDE_PATH"]."/addons/xform/manage/edit.inc.php";
