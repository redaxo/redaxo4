<?php

// include $REX["INCLUDE_PATH"]."/addons/xform/manage/functions/functions.inc.php";

$page = new rex_xform_manager();
$page->setType('com');
$page->setFilterTable('rex_com_user');
$page->setLinkVars(	array('page'=>'community','subpage'=>'user')	);
echo $page->getDataPage();

