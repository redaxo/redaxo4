<?

$PREPOST[0] = "PRE";
$PREPOST[1] = "POST";
$ASTATUS[0] = "ADD";
$ASTATUS[1] = "EDIT";
$ASTATUS[2] = "DELETE";

if ($subpage == "actions")
{
	title("Module: Actions","&nbsp;&nbsp;&nbsp;<a href=index.php?page=module&subpage=modules>Modules</a> | <a href=index.php?page=module&subpage=actions>Actions</a> ");
	include $REX[INCLUDE_PATH]."/pages/module.action.inc.php";
}else
{
	title("Module","&nbsp;&nbsp;&nbsp;<a href=index.php?page=module&subpage=modules>Modules</a> | <a href=index.php?page=module&subpage=actions>Actions</a> ");
	include $REX[INCLUDE_PATH]."/pages/module.modules.inc.php";
}

?>