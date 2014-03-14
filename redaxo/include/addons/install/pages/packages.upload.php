<?php

/** @var array $REX */
/** @var i18n $I18N */

$addonkey = rex_request('addonkey', 'string');
$file_id = rex_request('file', 'string');
$addons = array();

try {
    if ($func == 'upload') {
        $api = new rex_api_install_package_upload();
        $info = $api->execute();
        echo rex_info($info);
        $file_id = null;
    } elseif ($func == 'delete') {
        $api = new rex_api_install_package_delete();
        $info = $api->execute();
        echo rex_info($info);
        $file_id = null;
    }
} catch (rex_install_functional_exception $e) {
    echo rex_warning($e->getMessage());
    $file_id = null;
}

try {
    $addons = rex_install_packages::getMyPackages();
} catch (rex_install_functional_exception $e) {
    echo rex_warning($e->getMessage());
    $addonkey = '';
}

if ($addonkey && isset($addons[$addonkey])) {
    $addon = $addons[$addonkey];
    $file_id = rex_request('file', 'string');

    if ($file_id) {
        $new = $file_id == 'new';
        $file = $new
            ? array('version' => '', 'description' => '', 'status' => 1, 'redaxo_versions' => array($REX['VERSION'] . '.' . $REX['SUBVERSION'] . '.x'))
            : $addon['files'][$file_id];

        $newVersion = OOAddon::getVersion($addonkey);

        $redaxo_select = new rex_select;
        $redaxo_select->setName('upload[redaxo][]');
        $redaxo_select->setId('install-packages-upload-redaxo');
        $redaxo_select->setAttribute('class', 'rex-form-select');
        $redaxo_select->setSize(4);
        $redaxo_select->setMultiple(true);
        $redaxo_select->addOption('5.0.x', '5.0.x');
        $redaxo_select->addOption('4.6.x', '4.6.x');
        $redaxo_select->addOption('4.5.x', '4.5.x');
        $redaxo_select->addOption('4.4.x', '4.4.x');
        $redaxo_select->addOption('4.3.x', '4.3.x');
        $redaxo_select->setSelected($file['redaxo_versions']);

        $uploadCheckboxDisabled = '';
        $hiddenField = '';
        if ($new || !OOAddon::isAvailable($addonkey)) {
            $uploadCheckboxDisabled = ' disabled="disabled"';
            $hiddenField = '<input type="hidden" name="upload[upload_file]" value="' . ((integer) $new) . '" />';
        }

        $content = '
    <div class="rex-addon-output">
        <h2 class="rex-hl2">' . htmlspecialchars($addonkey) . '</h2>


        <div class="rex-form">
            <form action="index.php?page=install&amp;subpage=upload&amp;func=upload&amp;addonkey=' . htmlspecialchars($addonkey) . '&amp;file=' . htmlspecialchars($file_id) . '" method="post">
                <fieldset class="rex-form-col-1">
                    <legend>' . $I18N->msg($new ? 'install_version_add' : 'install_version_edit') . '</legend>

                    <div class="rex-form-wrapper">
            ';

        $content .= '
            <div class="rex-form-row">
                <p class="rex-form-col-a rex-form-read">
                    <label for="install-packages-upload-version">' . $I18N->msg('install_version') . '</label>
                    <span id="install-packages-upload-version" class="rex-form-read">' . htmlspecialchars($new ? $newVersion : $file['version']) . '</span>
                    <input type="hidden" name="upload[oldversion]" value="' . htmlspecialchars($file['version']) . '" />
                </p>
            </div>';

        $content .= '
            <div class="rex-form-row">
                <p class="rex-form-col-a rex-form-select">
                    <label for="install-packages-upload-redaxo">REDAXO</label>
                    ' . $redaxo_select->get() . '
                </p>
            </div>';

        $content .= '
            <div class="rex-form-row">
                <p class="rex-form-col-a rex-form-textarea">
                    <label for="install-packages-upload-description">' . $I18N->msg('install_description') . '</label>
                    <textarea id="install-packages-upload-description" name="upload[description]" cols="50" rows="15">' . htmlspecialchars($file['description']) . '</textarea>
                </p>
            </div>';

        $content .= '
            <div class="rex-form-row">
                <p class="rex-form-col-a rex-form-checkbox rex-form-label-right">
                    <input class="rex-form-checkbox" id="install-packages-upload-status" type="checkbox" name="upload[status]" value="1" ' . (!$new && $file['status'] ? 'checked="checked" ' : '') . '/>
                    <label for="install-packages-upload-status">' . $I18N->msg('install_online') . '</label>
                </p>
            </div>';

        $content .= '
            <div class="rex-form-row">
                <p class="rex-form-col-a rex-form-checkbox rex-form-label-right">
                    ' . $hiddenField . '
                    <input class="rex-form-checkbox" id="install-packages-upload-upload-file" type="checkbox" name="upload[upload_file]" value="1" ' . ($new ? 'checked="checked" ' : '') . $uploadCheckboxDisabled . '/>
                    <label for="install-packages-upload-upload-file">' . $I18N->msg('install_upload_file') . '</label>
                </p>
            </div>';

        if (OOAddon::isInstalled($addonkey) && is_dir(rex_path::addonAssets($addonkey))) {
            $content .= '
            <div class="rex-form-row">
                <p class="rex-form-col-a rex-form-checkbox rex-form-label-right">
                    <input class="rex-form-checkbox" id="install-packages-upload-replace-assets" type="checkbox" name="upload[replace_assets]" value="1" ' . ($new ? '' : 'disabled="disabled" ') . '/>
                    <label for="install-packages-upload-replace-assets">' . $I18N->msg('install_replace_assets') . '</label>
                </p>
            </div>';
        }

        if (is_dir(rex_path::addon($addonkey, 'tests'))) {
            $content .= '
            <div class="rex-form-row">
                <p class="rex-form-col-a rex-form-checkbox rex-form-label-right">
                    <input class="rex-form-checkbox" id="install-packages-upload-ignore-tests" type="checkbox" name="upload[ignore_tests]" value="1" checked="checked"' . ($new ? '' : 'disabled="disabled" ') . '/>
                    <label for="install-packages-upload-ignore-tests">' . $I18N->msg('install_ignore_tests') . '</label>
                </p>
            </div>';

        }

        $content .= '</div></fieldset>';

        $content .= '
        <fieldset class="rex-form-col-1">
          <div class="rex-form-wrapper">
            <div class="rex-form-row">
              <p class="rex-form-col-a rex-form-submit">
                <input class="rex-form-submit" type="submit" id="install-packages-upload-send" type="submit" name="upload[send]" value="' . $I18N->msg('install_send') . '"  />
                ';

                if (!$new) {
                    $content .= '<input class="rex-form-submit rex-form-submit-2" type="submit" id="install-packages-delete" value="' . $I18N->msg('delete') . '" onclick="if(confirm(\'' . $I18N->msg('delete') . ' ?\')) location.href=\'index.php?page=install&amp;subpage=upload&amp;addonkey=' . htmlspecialchars($addonkey) . '&amp;file=' . htmlspecialchars($file_id) . '&amp;func=delete\'; return false;" />';
                }


        $content .= '
              </p>
            </div>
          </div>
        </fieldset>
        ';


        $content .= '
            </form>
        </div>
    </div>';



        $content .= rex_content_block('<a class="rex-back" href="index.php?page=install&amp;subpage=upload&amp;addonkey=' . htmlspecialchars($addonkey) . '"><span class="rex-icon rex-icon-back"></span>' . $I18N->msg('install_back_to_overview') . '</a>');



        echo $content;

        if (!$new) {
            echo '
    <script type="text/javascript"><!--

        jQuery(function($) {
            $("#install-packages-upload-upload-file").change(function(){
                if($(this).is(":checked"))
                {
                    ' . ($newVersion != $file['version'] ? '$("#install-packages-upload-version").html("<span class=\'rex-strike\'>' . htmlspecialchars($file['version']) . '</span> <strong>' . $newVersion . '</strong>");' : '') . '
                    $("#install-packages-upload-replace-assets, #install-packages-upload-ignore-tests").removeAttr("disabled");
                }
                else
                {
                    $("#install-packages-upload-version").html("' . htmlspecialchars($file['version']) . '");
                    $("#install-packages-upload-replace-assets, #install-packages-upload-ignore-tests").attr("disabled", "disabled");
                }
            });
        });

    //--></script>';
        }

    } else {
        $icon = '';
        if (OOAddon::isAvailable($addonkey)) {
            $icon = '<a class="rex-i-element rex-i-generic-add" href="index.php?page=install&amp;subpage=upload&amp;addonkey=' .  htmlspecialchars($addonkey) . '&amp;file=new" title="' . $I18N->msg('install_version_add') . '"><span class="rex-i-element-text"></span></a>';
        }

        $content = '
    <div class="rex-addon-output">
        <h2 class="rex-hl2">' . htmlspecialchars($addonkey) . '</h2>
        <table class="rex-table">
            <colgroup>
              <col width="120" />
              <col width="*" />
            </colgroup>
            <tbody>
            <tr>
                <th>' . $I18N->msg('install_name') . '</th>
                <td>' . htmlspecialchars($addon['name']) . '</td>
            </tr>
            <tr>
                <th>' . $I18N->msg('install_author') . '</th>
                <td>' . htmlspecialchars($addon['author']) . '</td>
            </tr>
            <tr>
                <th>' . $I18N->msg('install_shortdescription') . '</th>
                <td>' . nl2br(htmlspecialchars($addon['shortdescription'])) . '</td>
            </tr>
            <tr>
                <th>' . $I18N->msg('install_description') . '</th>
                <td>' . nl2br(htmlspecialchars($addon['description'])) . '</td>
            </tr>
            </tbody>
        </table></div>';

        echo $content;

        $content = '
    <div class="rex-addon-output">
        <h2 class="rex-hl2">' . $I18N->msg('install_files') . '</h2>
        <table class="rex-table">
            <colgroup>
              <col width="40" />
              <col width="79" />
              <col width="79" />
              <col width="*" />
              <col width="153" />
              <col width="76" />
            </colgroup>
            <thead>
            <tr>
                <th class="rex-icon">' . $icon . '</th>
                <th class="rex-version">' . $I18N->msg('install_version') . '</th>
                <th>REDAXO</th>
                <th class="rex-description">' . $I18N->msg('install_description') . '</th>
                <th colspan="2" class="rex-function">' . $I18N->msg('install_status') . '</th>
            </tr>
            </thead>
            <tbody>';

        if (count($addon['files'])) {
            foreach ($addon['files'] as $fileId => $file) {
                $url = 'index.php?page=install&amp;subpage=upload&amp;addonkey=' .  htmlspecialchars($addonkey) . '&amp;file=' . htmlspecialchars($fileId);
                $status = $file['status'] ? 'online' : 'offline';
                $content .= '
                <tr>
                    <td class="rex-icon"><span class="rex-i-element rex-i-addon"><span class="rex-i-element-text"></span></span></td>
                    <td class="rex-version">' . htmlspecialchars($file['version']) . '</td>
                    <td class="rex-version">' . implode(', ', $file['redaxo_versions']) . '</td>
                    <td class="rex-description">' . nl2br(htmlspecialchars($file['description'])) . '</td>
                    <td class="rex-edit"><a class="rex-link rex-edit" href="' . $url . '">' . $I18N->msg('install_version_edit') . '</a></td>
                    <td class="rex-status"><span class="rex-status rex-' . $status . '">' . $I18N->msg('status_' . $status) . '</span></td>
                </tr>';
            }

        } else {

            $content .= '
                <tr class="rex-table-no-results">
                    <td colspan="6">' . $I18N->msg('install_my_packages_files_not_found', '<b>' . htmlspecialchars($addonkey) . '</b>') . '</td>
                </tr>';
        }

        $content .= '</tbody></table></div>';

        echo $content;

        echo rex_content_block('<a class="rex-back" href="index.php?page=install&amp;subpage=upload"><span class="rex-icon rex-icon-back"></span>' . $I18N->msg('install_back') . '</a>');

    }

} else {

    $content = '
    <div class="rex-addon-output">
        <h2 class="rex-hl2">' . $I18N->msg('install_my_packages') . '</h2>
        <table class="rex-table">
            <colgroup>
              <col width="40" />
              <col width="79" />
              <col width="*" />
              <col width="76" />
              <col width="77" />
            </colgroup>
         <thead>
            <tr>
                <th class="rex-icon"><a class="rex-i-refresh rex-i-element" href="index.php?page=install&amp;subpage=upload&amp;func=reload" title="' . $I18N->msg('install_reload') . '"><span class="rex-i-element-text">' . $I18N->msg('install_reload') . '</span></a></th>
                <th class="rex-key">' . $I18N->msg('install_key') . '</th>
                <th class="rex-name">' . $I18N->msg('install_name') . '</th>
                <th colspan="2" class="rex-function">' . $I18N->msg('install_status') . '</th>
            </tr>
         </thead>
         <tbody>';


    if (count($addons)) {
        foreach ($addons as $key => $addon) {
            $url = 'index.php?page=install&amp;subpage=upload&amp;addonkey=' . htmlspecialchars($key);
            $status = $addon['status'] ? 'online' : 'offline';
            $content .= '
                <tr>
                    <td class="rex-icon"><a href="' . $url . '" class="rex-link rex-view"><span class="rex-i-element rex-i-addon"><span class="rex-i-element-text"></span></span></a></td>
                    <td class="rex-key"><a href="' . $url . '" class="rex-link rex-view">' . htmlspecialchars($key) . '</a></td>
                    <td class="rex-name">' . htmlspecialchars($addon['name']) . '</td>
                    <td class="rex-view"><a href="' . $url . '" class="rex-link rex-view">' . $I18N->msg('install_view') . '</a></td>
                    <td class="rex-status"><span class="rex-status rex-' . $status . '">' . $I18N->msg('status_' . $status) . '</span></td>
                </tr>';
        }

    } else {

        $content .= '
            <tr class="rex-table-no-results">
                <td colspan="5">' . $I18N->msg('install_my_packages_not_found') . '</td>
            </tr>';
    }

    $content .= '</tbody></table></div>';

    echo $content;

}
