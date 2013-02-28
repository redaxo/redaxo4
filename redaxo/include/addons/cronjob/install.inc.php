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

$nexttime_file = $REX['INCLUDE_PATH'] .'/addons/cronjob/nexttime';

if(($state = rex_is_writable($nexttime_file)) !== true)
  $error .= $state;

$log_folder = $REX['INCLUDE_PATH'] .'/addons/cronjob/logs/';

if(($state = rex_is_writable($log_folder)) !== true)
  $error .= $state;

if ($error != '')
  $REX['ADDON']['installmsg']['cronjob'] = $error;
else
  $REX['ADDON']['install']['cronjob'] = true;