<?


if ($subpage == "actions" && $REX_USER->isValueOf("rights","action[]"))
{
	if ($REX_USER->isValueOf("rights","action[]")) title("Module: Actions","&nbsp;&nbsp;&nbsp;<a href=index.php?page=module&subpage=modules>Modules</a> | <a href=index.php?page=module&subpage=actions>Actions</a> ");
	else title("Module","");
	include $REX[INCLUDE_PATH]."/pages/module/module.action.inc.php";
}else
{
	if ($REX_USER->isValueOf("rights","action[]")) title("Module","&nbsp;&nbsp;&nbsp;<a href=index.php?page=module&subpage=modules>Modules</a> | <a href=index.php?page=module&subpage=actions>Actions</a> ");
	else title("Module","");
	include $REX[INCLUDE_PATH]."/pages/module/module.modules.inc.php";
}

?>