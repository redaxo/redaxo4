<?php
/**
 * 
 * @package redaxo3
 * @version $Id$
 */
 
// Parameter
$Basedir = dirname(__FILE__);

$page = rex_request('page', 'string');
$subpage = rex_request('subpage', 'string');
$func = rex_request('func', 'string');

include $REX['INCLUDE_PATH'].'/layout/top.php';

$subpages = array(
  array('','Konfiguration'),
  array('example','Beispiel'),
);

rex_title('PHPMailer', $subpages);

switch($subpage)
{
    case 'example':
        require $Basedir .'/example.inc.php';
    break;
    default:
        require $Basedir .'/settings.inc.php';
}

include $REX['INCLUDE_PATH'].'/layout/bottom.php';

?>