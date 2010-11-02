<?php

$subpage = rex_request("subpage","string");

$page = new rex_xform_manager();
$page->setType('em');

$page->setFilterTable($subpage);

$page->setLinkVars(	array('page'=>'editme','subpage'=>$subpage)	);
echo $page->getDataPage();

?>