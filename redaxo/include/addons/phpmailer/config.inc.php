<?php

/**
 * PHPMailer Addon
 *  
 * @author staab[at]public-4u[dot]de Markus Staab
 * 
 * @package redaxo3
 * @version $Id$
 */

$mypage = 'phpmailer';

$REX['ADDON']['rxid'][$mypage] = '93';
$REX['ADDON']['page'][$mypage] = $mypage;    
$REX['ADDON']['name'][$mypage] = 'PHPMailer';
$REX['ADDON']['perm'][$mypage] = 'phpmailer[]';
$REX['ADDON']['version'][$mypage] = "1.0";
$REX['ADDON']['author'][$mypage] = "Markus Staab";
// $REX['ADDON']['supportpage'][$mypage] = "";

$REX['PERM'][] = 'phpmailer[]';
$I18N_A93 = new i18n($REX['LANG'], $REX['INCLUDE_PATH'].'/addons/'.$mypage.'/lang/'); 

require_once($REX['INCLUDE_PATH']. '/addons/phpmailer/classes/class.phpmailer.inc.php');
require_once($REX['INCLUDE_PATH']. '/addons/phpmailer/classes/class.rex_mailer.inc.php');

?>