<?php

/**
 * Cronjob Addon
 *
 * @author gharlan[at]web[dot]de Gregor Harlan
 *
 * @package redaxo4
 * @version svn:$Id$
 */

$error = '';

if ($error != '') {
    $REX['ADDON']['installmsg']['cronjob'] = $error;
} else {
    $REX['ADDON']['install']['cronjob'] = true;
}
