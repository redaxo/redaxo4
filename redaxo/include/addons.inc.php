<?php

// ----------------- addons
unset($REX[ADDON][status]);

// ----------------- DONT EDIT BELOW THIS
// --- DYN

$REX[ADDON][install][import_export] = 1;
$REX[ADDON][status][import_export] = 1;

$REX[ADDON][install][seminare] = 1;
$REX[ADDON][status][seminare] = 0;

$REX[ADDON][install][shop] = 0;
$REX[ADDON][status][shop] = 0;

// --- /DYN
// ----------------- /DONT EDIT BELOW THIS


for($i=0;$i<count($REX[ADDON][status]);$i++)
{
	if (current($REX[ADDON][status]) == 1) include $REX[INCLUDE_PATH]."/addons/".key($REX[ADDON][status])."/config.inc.php";
	next($REX[ADDON][status]);
}

?>