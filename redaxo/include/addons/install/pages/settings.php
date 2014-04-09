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
    <div class="rex-addon-output">
        <h2 class="rex-hl2">' . $I18N->msg('install_subpage_settings') . '</h2>

        <div class="rex-form">
            <form action="index.php?page=install&subpage=settings" method="post">
                <fieldset class="rex-form-col-1">
                    <legend>' . $I18N->msg('install_settings_general') . '</legend>

                    <div class="rex-form-wrapper">';

$content .= '
                        <div class="rex-form-row">
                            <p class="rex-form-col-a rex-form-checkbox rex-form-label-right">
                                <input class="rex-form-checkbox" id="install-settings-backups" type="checkbox" name="settings[backups]" value="1" ' . (OOAddon::getProperty('install', 'backups') ? 'checked="checked" ' : '') . '/>
                                <label for="install-settings-backups">' . $I18N->msg('install_settings_backups') . '</label>
                            </p>
                        </div>';

$content .= '       </div>
                </fieldset>

                <fieldset>
                    <legend>' . $I18N->msg('install_settings_myredaxo_account') . '</legend>
                    <div class="rex-form-wrapper">';

$content .= '
                        <div class="rex-form-row">
                            <p class="rex-form-col-a rex-form-text">
                                <label for="install-settings-api-login">' . $I18N->msg('install_settings_api_login') . '</label>
                                <input id="install-settings-api-login" class="rex-form-text" type="text" name="settings[api_login]" value="' . OOAddon::getProperty('install', 'api_login') . '" />
                            </p>
                        </div>';

$content .= '
                        <div class="rex-form-row">
                            <p class="rex-form-col-a rex-form-text">
                                <label for="install-settings-api-key">' . $I18N->msg('install_settings_api_key') . '</label>
                                <input id="install-settings-api-key" class="rex-form-text" type="text" name="settings[api_key]" value="' . OOAddon::getProperty('install', 'api_key') . '" />
                            </p>
                        </div>';

$content .= '       </div>
                </fieldset>';

$content .= '
                <fieldset class="rex-form-col-1">
                  <div class="rex-form-wrapper">
                    <div class="rex-form-row">
                        <p class="rex-form-col-a rex-form-submit">
                            <input class="rex-form-submit" type="submit" name="settings[save]" value="' . $I18N->msg('form_save') . '"  />
                        </p>
                    </div>
                  </div>
                </fieldset>

        </form>
    </div>
</div>';

echo $content;
