<?php

/**
 * PHPMailer Addon
 *
 * @author staab[at]public-4u[dot]de Markus Staab
 * @author <a href="http://www.public-4u.de">www.public-4u.de</a>
 *
 * @package redaxo4
 * @version $Id$
 */

$error = '';

$I18N_A93 = new i18n($REX['LANG'], $REX['INCLUDE_PATH'].'/addons/phpmailer/lang/');
$settings_file = $REX['INCLUDE_PATH'] .'/addons/phpmailer/classes/class.rex_mailer.inc.php';

if(!rex_is_writable($settings_file))
  $error = $I18N_A93->msg('config_file_not_writable');

if ($error != '')
  $REX['ADDON']['installmsg']['phpmailer'] = $error;
else
  $REX['ADDON']['install']['phpmailer'] = true;

?>