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

if ($addonkey && isset($addons[$addonkey]) && !is_dir(rex_path::addon($addonkey))) {
    $addon = $addons[$addonkey];

    $content = '
    <div class="rex-addon-output">
        <h2 class="rex-hl2">' . htmlspecialchars($addonkey) . '</small></h2>

        <table id="rex-table-install-packages-information" class="rex-table">
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
        </table></div>';


    $content .= '
    <div class="rex-addon-output">
        <h3 class="rex-hl2">' . $I18N->msg('install_files') . '</h3>
        <table id="rex-table-install-packages-files" class="rex-table">
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
                <td class="rex-function"><a class="rex-link rex-download" href="index.php?page=install&amp;subpage=add&amp;addonkey=' . htmlspecialchars($addonkey) . '&amp;func=add&amp;file=' . htmlspecialchars($fileId)  . '">' . $I18N->msg('install_download') . '</a></td>
            </tr>';
    }

    $content .= '</tbody></table></div>';

    $content .= rex_content_block('<a class="rex-back" href="index.php?page=install&amp;subpage=add">' . $I18N->msg('install_back_to_overview') . '</a>');

    echo $content;


} else {

    echo rex_content_block('<input type="text" id="rex-install-addon-search" class="rex-form-text" placeholder="Suchen…" style="width: 300px"/>');

    $content = '
    <div class="rex-addon-output">
        <h2 class="rex-hl2">' . $I18N->msg('install_addons_found', count($addons)) . '</h2>
        <table id="rex-table-install-packages-addons" class="rex-table rex-table-striped">
            <colgroup>
              <col width="40">
              <col width="153">
              <col width="*">
              <col width="153">
            </colgroup>
         <thead>
            <tr>
                <th class="rex-icon"><a class="rex-i-refresh rex-i-element" href="index.php?page=install&amp;subpage=add&amp;func=reload" title="' . $I18N->msg('install_reload') . '"><span class="rex-i-element-text">' . $I18N->msg('install_reload') . '</span></a></th>
                <th class="rex-key">' . $I18N->msg('install_key') . '</th>
                <th class="rex-shortdescription">' . $I18N->msg('install_shortdescription') . '</th>
                <th class="rex-function">' . $I18N->msg('install_header_function') . '</th>
            </tr>
         </thead>
         <tbody>';

    foreach ($addons as $key => $addon) {
        if (is_dir(rex_path::addon($key))) {
            $content .= '
                <tr>
                    <td class="rex-icon"><span class="rex-i-element rex-i-addon"><span class="rex-i-element-text"></span></span></td>
                    <td class="rex-key">' . htmlspecialchars($key) . '</td>
                    <td class="rex-shortdescription"><h4>' . htmlspecialchars($addon['name']) . '</h4><i>' . htmlspecialchars($addon['author']) . '</i><br /><br />' . nl2br(htmlspecialchars($addon['shortdescription'])) . '</td>
                    <td class="rex-view">' . $I18N->msg('install_addon_already_exists') . '</td>
                </tr>';
        } else {
            $url = 'index.php?page=install&amp;subpage=add&amp;addonkey=' . htmlspecialchars($key);
            $content .= '
                <tr>
                    <td class="rex-icon"><a href="' . $url . '"><span class="rex-i-element rex-i-addon"><span class="rex-i-element-text"></span></span></a></td>
                    <td class="rex-key"><a href="' . $url . '">' . htmlspecialchars($key) . '</a></td>
                    <td class="rex-shortdescription"><h4>' . htmlspecialchars($addon['name']) . '</h4><i>' . htmlspecialchars($addon['author']) . '</i><br /><br />' . nl2br(htmlspecialchars($addon['shortdescription'])) . '</td>
                    <td class="rex-view"><a href="' . $url . '" class="rex-link rex-view">' . $I18N->msg('install_view') . '</a></td>
                </tr>';
        }
    }

    $content .= '</tbody></table></div>';

    $content .= '
        <script type="text/javascript">
        <!--

        jQuery(function($) {
            var table = $("#rex-table-install-packages-addons");
            $("#rex-install-addon-search").keyup(function () {
                table.find("tr").show();
                var search = $(this).val().toLowerCase();
                if (search) {
                    table.find("tr").each(function () {
                        var tr = $(this);
                        if (tr.text().toLowerCase().indexOf(search) < 0) {
                            tr.hide();
                        }
                    });
                }
            });
        });

        //-->
        </script>
    ';


    echo $content;

}
