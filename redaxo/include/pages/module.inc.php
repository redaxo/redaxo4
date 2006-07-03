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
      $title = $I18N->msg("modules").': '.$I18N->msg("actions");
      $file = 'module.action.inc.php';
      break;
    }
  default :
    {
      $title = $I18N->msg("modules");
      $file = 'module.modules.inc.php';
      break;
    }
}

rex_title($title, array (array ('', $I18N->msg('modules')), array ('actions', $I18N->msg('actions'))));
include $REX['INCLUDE_PATH'].'/pages/'.$file;
?>