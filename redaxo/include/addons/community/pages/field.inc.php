<?php

$page = new rex_xform_manager();
$page->setType('com');
// $page->setFilterTable('rex_com_group');
$page->setLinkVars(	array('page'=>'community','subpage'=>'field')	);
echo $page->getFieldPage();

