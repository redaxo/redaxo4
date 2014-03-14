<?php

/** @var i18n $I18N */

$addonkey = rex_request('addonkey', 'string');

$addons = array();

$content = '';

try {
    if ($func == 'update') {
        $api = new rex_api_install_package_update();
        $info = $api->execute();
        echo rex_info($info);
        $addonkey = '';
    }
} catch (rex_install_functional_exception $e) {
    echo rex_warning($e->getMessage());
    $addonkey = '';
}

try {
    $addons = rex_install_packages::getUpdatePackages();
} catch (rex_install_functional_exception $e) {
    $content .= rex_warning($e->getMessage());
    $addonkey = '';
}

if ($addonkey && isset($addons[$addonkey])) {

    $addon = $addons[$addonkey];


    $content .= '
    <div class="rex-addon-output">
        <h2 class="rex-hl2">' . htmlspecialchars($addonkey) . '</h2>
        <table id="rex-install-packages-information" class="rex-table">
            <colgroup>
              <col width="120" />
              <col width="*" />
            </colgroup>
            <tbody>
            <tr>
                <th class="rex-term">' . $I18N->msg('install_name') . '</th>
                <td class="rex-description">' . htmlspecialchars($addon['name']) . '</td>
            </tr>
            <tr>
                <th class="rex-term">' . $I18N->msg('install_author') . '</th>
                <td class="rex-description">' . htmlspecialchars($addon['author']) . '</td>
            </tr>
            <tr>
                <th class="rex-term">' . $I18N->msg('install_shortdescription') . '</th>
                <td class="rex-description">' . nl2br(htmlspecialchars($addon['shortdescription'])) . '</td>
            </tr>
            <tr>
                <th class="rex-term">' . $I18N->msg('install_description') . '</th>
                <td class="rex-description">' . nl2br(htmlspecialchars($addon['description'])) . '</td>
            </tr>
            </tbody>
        </table></div>

    <div class="rex-addon-output">
        <h3 class="rex-hl2">' . $I18N->msg('install_files') . '</h3>
        <table class="rex-table rex-install-packages-files">
            <colgroup>
              <col width="40" />
              <col width="79" />
              <col width="*" />
              <col width="153" />
            </colgroup>
            <thead>
            <tr>
                <th class="rex-slim"></th>
                <th class="rex-version">' . $I18N->msg('install_version') . '</th>
                <th class="rex-description">' . $I18N->msg('install_description') . '</th>
                <th class="rex-function">' . $I18N->msg('install_header_function') . '</th>
            </tr>
            </thead>
            <tbody>';

    foreach ($addon['files'] as $fileId => $file) {
        $content .= '
            <tr>
                <td class="rex-icon"><span class="rex-i-element rex-i-addon"><span class="rex-i-element-text"></span></span></td>
                <td class="rex-version">' . htmlspecialchars($file['version']) . '</td>
                <td class="rex-description">' . nl2br(htmlspecialchars($file['description'])) . '</td>
                <td class="rex-update"><a href="index.php?page=install&amp;addonkey=' . htmlspecialchars($addonkey) . '&amp;func=update&amp;file=' . htmlspecialchars($fileId) . '">' . $I18N->msg('install_update') . '</a></td>
            </tr>';
    }

    $content .= '</tbody></table></div>';

    $content .= rex_content_block('<a class="rex-back" href="index.php?page=install&amp;">' . $I18N->msg('install_back_to_overview') . '</a>');

} else {

    $content .= '
    <div class="rex-addon-output">
        <h2 class="rex-hl2">' . $I18N->msg('install_available_update' . (count($addons) != 1 ? 's' : ''), count($addons)) . '</h2>
        <table id="rex-install-packages-addons" class="rex-table">
            <colgroup>
              <col width="40" />
              <col width="153" />
              <col width="*" />
              <col width="153" />
              <col width="153" />
            </colgroup>
            <thead>
            <tr>
                <th class="rex-icon"><a class="rex-i-refresh rex-i-element" href="index.php?page=install&amp;subpage=&amp;func=reload" title="' . $I18N->msg('install_reload') . '"><span class="rex-i-element-text">' . $I18N->msg('install_reload') . '</span></a></th>
                <th class="rex-key">' . $I18N->msg('install_key') . '</th>
                <th class="rex-name">' . $I18N->msg('install_name') . '</th>
                <th class="rex-version">' . $I18N->msg('install_existing_version') . '</th>
                <th class="rex-version">' . $I18N->msg('install_available_versions') . '</th>
            </tr>
            </thead>
            <tbody>';

    if (count($addons)) {

        foreach ($addons as $key => $addon) {
            $availableVersions = array();
            foreach ($addon['files'] as $file) {
                $availableVersions[] = $file['version'];
            }
            $url = 'index.php?page=install&amp;addonkey=' . htmlspecialchars($key);

            $content .= '
                <tr>
                    <td class="rex-icon"><a href="' . $url . '"><span class="rex-i-element rex-i-addon"><span class="rex-i-element-text"></span></span></a></td>
                    <td class="rex-key"><a href="' . $url . '">' . htmlspecialchars($key) . '</a></td>
                    <td class="rex-name">' . htmlspecialchars($addon['name']) . '</td>
                    <td class="rex-version">' . htmlspecialchars(OOAddon::getVersion($key)) . '</td>
                    <td class="rex-version">' . htmlspecialchars(implode(', ', $availableVersions)) . '</td>
                </tr>';
        }
    } else {

        $content .= '
            <tr class="rex-table-no-results">
                <td colspan="5">' . $I18N->msg('install_installed_addons_not_found') . '</td>
            </tr>';
    }

    $content .= '</tbody></table></div>';

}

echo $content;
