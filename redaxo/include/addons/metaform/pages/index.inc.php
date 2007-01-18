<?php

/**
 * MetaForm Addon
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * @package redaxo3
 * @version $Id$
 */
 
// Parameter
$Basedir = dirname(__FILE__);

$page = rex_request('page', 'string');
$subpage = rex_request('subpage', 'string');
$func = rex_request('func', 'string');


// Include Header and Navigation
include $REX['INCLUDE_PATH'].'/layout/top.php';

// Build Subnavigation 
$subpages = array(
//  array('','Abschnitte'),
);

rex_title('Metaformular erweitern');
// Include Current Page
switch($subpage)
{
    case 'fields':
        require $Basedir .'/fields.inc.php';
    break;
    default:
        require $Basedir .'/sections.inc.php';
}

// Include Footer 
include $REX['INCLUDE_PATH'].'/layout/bottom.php';
?>