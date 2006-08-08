<?php
include $REX['INCLUDE_PATH']."/layout/top.php";

$subpages = array (array ('', 'Redaxo Admin Suche'), array ('gen_index', 'Such Index erneuern'), array ('gen_module', 'Suche in ein Modul einbauen'),);

rex_title("Such Index", $subpages);

if (!isset ($subpage))
  $subpage = '';
if (!isset ($msg))
  $msg = '';
if (!isset ($rexsearch))
  $rexsearch = '';

if ($msg != "")
  echo "<table border=0 cellpadding=5 cellspacing=1 width=770><tr><td class=warning>$msg</td></tr></table><br>";

$Basedir = dirname(__FILE__);

switch ($subpage)
{
  case 'gen_index' :
    require $Basedir.'/searchindex.inc.php';
    break;
  case 'gen_module' :
    require $Basedir.'/module.inc.php';
    break;
  default :
    require $Basedir.'/adminsearch.inc.php';
}

include $REX['INCLUDE_PATH']."/layout/bottom.php";
?>