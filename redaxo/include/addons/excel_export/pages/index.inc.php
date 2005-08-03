<?php

/*
    Glossar Addon by <a href="mailto:staab@public-4u.de">Markus Staab</a>
    <a href="http://www.public-4u.de">www.public-4u.de</a>
    06.07.2005
    Version RC2
*/


// *************************************** INCLUDES

$Basedir = dirname( __FILE__);

// Settings
require_once $Basedir .'/../settings.inc.php';

// Functions
require_once $Basedir .'/../functions/function_xls.inc.php';
require_once $Basedir .'/../functions/function_compat.inc.php';

// Classes
require_once $Basedir .'/../classes/class.rexform.inc.php';
require_once $Basedir .'/../classes/class.rexlist.inc.php';
require_once $Basedir .'/../classes/class.rexselect.inc.php';
require_once $Basedir .'/../classes/class.table.inc.php';

// *************************************** MAIN

include layout_top();

// Setup subpages
$subpages = array( 
//   array( 'export', '&Uuml;bersicht'), 
//   array( 'settings', 'Einstellungen') 
);

addon_title('Inhalte ins Excel-Format Exportieren', $subpages);

switch($subpage){
    
    case 'settings':
        require $Basedir .'/settings.inc.php';
    break;
    
    default:
        require $Basedir .'/export.inc.php';
}

// Rex layout-bottom einbinden
include layout_bottom();

?>