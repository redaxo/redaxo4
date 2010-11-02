<?php

$table_name = rex_request('table_name',"string");

$page = new rex_xform_manager();
$page->setType('em');
$page->setFilterTable($table_name);
$page->setLinkVars(	array('page'=>'editme','subpage'=>'field')	);
echo $page->getFieldPage();

?>