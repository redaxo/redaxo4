<?php

/**
 * PHPMailer Addon
 *  
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 * 
 * @package redaxo3
 * @version $Id$
 */

$mypage = 'phpmailer';

$REX['ADDON']['rxid'][$mypage] = '93';
$REX['ADDON']['page'][$mypage] = $mypage;    
$REX['ADDON']['name'][$mypage] = 'PHPMailer';
$REX['ADDON']['perm'][$mypage] = 'phpmailer[]';

$REX['PERM'][] = 'phpmailer[]';

require_once($REX['INCLUDE_PATH']. '/addons/phpmailer/classes/class.phpmailer.inc.php');
require_once($REX['INCLUDE_PATH']. '/addons/phpmailer/classes/class.rex_mailer.inc.php');

?>