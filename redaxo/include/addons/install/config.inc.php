<?php

/** @var i18n $I18N */

$mypage = 'install';

$REX['ADDON']['version'][$mypage] = '1.0.1';
$REX['ADDON']['author'][$mypage] = 'Gregor Harlan';
$REX['ADDON']['supportpage'][$mypage] = 'redaxo.org/de/forum';

if ($REX['REDAXO']) {
    $I18N->appendFile(__DIR__ . '/lang/');

    $REX['ADDON']['name'][$mypage] = $I18N->msg('install_title');
    $REX['ADDON']['perm'][$mypage] = 'admin[]';

    $REX['ADDON']['navigation'][$mypage]['block'] = 'system';

    $REX['ADDON']['pages'][$mypage] = array(
        array('',         $I18N->msg('install_subpage_update')),
        array('add',      $I18N->msg('install_subpage_add')),
        array('upload',   $I18N->msg('install_subpage_upload')),
        array('settings', $I18N->msg('install_subpage_settings'))
    );

    $REX['ADDON']['backups']['install'] = false;
    $REX['ADDON']['api_login']['install'] = '';
    $REX['ADDON']['api_key']['install'] = '';

    $settings = rex_path::addonData('install', 'settings.inc.php');
    if (file_exists($settings)) {
        include $settings;
    }
}
