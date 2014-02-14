<?php

/** @var i18n $I18N */

$content = '';

$settings = rex_post('settings', array(
    array('backups', 'bool', false),
    array('api_login', 'string'),
    array('api_key', 'string')
), null);

if (is_array($settings)) {
    $settingsContent = "<?php\n\n";
    foreach ($settings as $key => $value) {
        $settingsContent .= "\$REX['ADDON'][" . var_export($key, true) . "]['install'] = " . var_export($value, true) . ";\n";
        OOAddon::setProperty('install', $key, $value);
    }
    if (rex_file::put(rex_path::addonData('install', 'settings.inc.php'), $settingsContent)) {
        echo rex_info($I18N->msg('install_settings_saved'));
    } else {
        echo rex_warning($I18N->msg('install_settings_not_saved'));
    }
    rex_install_webservice::deleteCache();
}

$content .= '
    <div class="rex-form">
        <form action="index.php?page=install&subpage=settings" method="post">
            <fieldset>
                <h2>' . $I18N->msg('install_settings_general') . '</h2>';

$content .= '<label for="install-settings-backups">' . $I18N->msg('install_settings_backups') . '</label>';
$content .= '<input id="install-settings-backups" type="checkbox" class="rex-form-checkbox" name="settings[backups]" value="1" ' . (OOAddon::getProperty('install', 'backups') ? 'checked="checked" ' : '') . '/>';

$content .= '
            </fieldset>
            <fieldset>
                <h2>' . $I18N->msg('install_settings_myredaxo_account') . '</h2>';

$content .= '<label for="install-settings-api-login">' . $I18N->msg('install_settings_api_login') . '</label>';
$content .='<input id="install-settings-api-login" class="rex-form-text" type="text" name="settings[api_login]" value="' . OOAddon::getProperty('install', 'api_login') . '" />';

$content .= '<label for="install-settings-api-key">' . $I18N->msg('install_settings_api_key') . '</label>';
$content .= '<input id="install-settings-api-key" class="rex-form-text" type="text" name="settings[api_key]" value="' . OOAddon::getProperty('install', 'api_key') . '" />';

$content .= '
                </fieldset>';

$content .= '<button class="rex-button" type="submit" name="settings[save]" value="1">' . $I18N->msg('form_save') . '</button>';

$content .= '
        </form>
    </div>';

echo $content;
