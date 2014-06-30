<?php

/**
 * @var array $REX
 * @var i18n $I18N
 */

include $REX['INCLUDE_PATH'] . '/layout/top.php';

$page = rex_request('page', 'string');
$subpage = rex_request('subpage', 'string');
$func = rex_request('func', 'string');

rex_title($I18N->msg('install_title'), $REX['ADDON']['pages']['install']);

switch ($subpage) {
    case 'add':      $file = 'packages.add';    break;
    case 'upload':   $file = 'packages.upload'; break;
    case 'settings': $file = 'settings';        break;
    default:         $file = 'packages.update';
}

$dir = $REX['INCLUDE_PATH'] . '/addons/install/lib/';
require_once $dir . 'webservice.php';
require_once $dir . 'packages.php';
require_once $dir . 'archive.php';
require_once $dir . 'exception.php';
require_once $dir . 'api_package_download.php';
require_once $dir . 'api_package_add.php';
require_once $dir . 'api_package_delete.php';
require_once $dir . 'api_package_update.php';
require_once $dir . 'api_package_upload.php';

if ('reload' === $func) {
    rex_install_webservice::deleteCache();
    $func = '';
}

include __DIR__ . '/' . $file . '.php';

include $REX['INCLUDE_PATH'] . '/layout/bottom.php';
