<?php

/** @var i18n $I18N */

$addonkey = rex_request('addonkey', 'string');
$addons = array();

try {
    if ($func == 'add') {
        $api = new rex_api_install_package_add();
        $info = $api->execute();
        echo rex_info($info);
        $addonkey = '';
    }
} catch (rex_install_functional_exception $e) {
    echo rex_warning($e->getMessage());
    $addonkey = '';
}

try {
    $addons = rex_install_packages::getAddPackages();
} catch (rex_install_functional_exception $e) {
    echo rex_warning($e->getMessage());
    $addonkey = '';
}

if ($addonkey && isset($addons[$addonkey])) {
    $addon = $addons[$addonkey];

    $content = '
        <h2><b>' . htmlspecialchars($addonkey) . '</b> ' . $I18N->msg('install_information') . '</h2>

        <table id="rex-table-install-packages-information" class="rex-table">
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
        </table>';


    $content .= '
        <h2>' . $I18N->msg('install_files') . '</h2>
        <table id="rex-table-install-packages-files" class="rex-table">
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
                <td class="rex-function"><a class="rex-link rex-download" href="index.php?page=install&amp;subpage=add&amp;addonkey=' . htmlspecialchars($addonkey) . '&amp;func=add&amp;file=' . htmlspecialchars($fileId)  . '">' . $I18N->msg('install_download') . '</a></td>
            </tr>';
    }

    $content .= '</tbody></table>';

    echo $content;


} else {

    $content = '
        <h2>' . $I18N->msg('install_addons_found', count($addons)) . '</h2>
        <table id="rex-table-install-packages-addons" class="rex-table rex-table-striped">
         <thead>
            <tr>
                <th class="rex-icon"></th>
                <th class="rex-key">' . $I18N->msg('install_key') . '</th>
                <th class="rex-name rex-author">' . $I18N->msg('install_name') . ' / ' . $I18N->msg('install_author') . '</th>
                <th class="rex-shortdescription">' . $I18N->msg('install_shortdescription') . '</th>
                <th class="rex-function">' . $I18N->msg('install_header_function') . '</th>
            </tr>
         </thead>
         <tbody>';

    foreach ($addons as $key => $addon) {
        $url = 'index.php?page=install&amp;subpage=add&amp;addonkey=' . htmlspecialchars($key);
        $content .= '
            <tr>
                <td class="rex-icon"><span class="rex-i-element rex-i-addon"><span class="rex-i-element-text"></span></span></td>
                <td class="rex-key"><a href="' . $url . '">' . htmlspecialchars($key) . '</a></td>
                <td class="rex-name rex-author">' . htmlspecialchars($addon['name']) . '<br />' . htmlspecialchars($addon['author']) . '</td>
                <td class="rex-shortdescription">' . nl2br(htmlspecialchars($addon['shortdescription'])) . '</td>
                <td class="rex-view"><a href="' . $url . '" class="rex-link rex-view">' . $I18N->msg('install_view') . '</a></td>
            </tr>';
    }

    $content .= '</tbody></table>';


    echo $content;

}
