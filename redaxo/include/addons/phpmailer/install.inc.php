<?php

/**
 * PHPMailer Addon
 *
 * @author markus[dot]staab[at]redaxo[dot]de Markus Staab
 *
 *
 * @package redaxo4
 * @version svn:$Id$
 */

$error = '';

if ($error != '') {
    $REX['ADDON']['installmsg']['phpmailer'] = $error;
} else {
    $REX['ADDON']['install']['phpmailer'] = true;
}
