<?php
$PREPOST[0] = "PRE";
$PREPOST[1] = "POST";
$ASTATUS[0] = "ADD";
$ASTATUS[1] = "EDIT";
$ASTATUS[2] = "DELETE";

if (!isset ($subpage))
{
  $subpage = '';
}

switch ($subpage)
{
  case 'actions' :
    {
      $title = 'Module: Actions';
      $file = 'module.action.inc.php';
      break;
    }
  default :
    {
      $title = 'Module';
      $file = 'module.modules.inc.php';
      break;
    }
}

rex_title($title, array (array ('', 'Modules'), array ('actions', 'Actions')));
include $REX['INCLUDE_PATH'].'/pages/'.$file;
?>