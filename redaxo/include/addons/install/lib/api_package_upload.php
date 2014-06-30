<?php

/**
 * @package redaxo\install
 */
class rex_api_install_package_upload
{
    public function execute()
    {
        global $I18N;

        $addonkey = rex_request('addonkey', 'string');
        $upload = rex_request('upload', array(
            array('upload_file', 'bool'),
            array('oldversion', 'string'),
            array('redaxo', 'array[string]'),
            array('description', 'string'),
            array('status', 'int'),
            array('replace_assets', 'bool'),
            array('ignore_tests', 'bool')
        ));
        $file = array();
        $archive = null;
        $file['version'] = $upload['upload_file'] ? OOAddon::getVersion($addonkey) : $upload['oldversion'];
        $file['redaxo_versions'] = $upload['redaxo'];
        $file['description'] = stripslashes($upload['description']);
        $file['status'] = $upload['status'];

        if ($upload['upload_file']) {
            $archive = rex_path::addonCache('install', md5($addonkey . time()) . '.zip');
            $exclude = array();
            if ($upload['replace_assets']) {
                $exclude[] = 'files';
            }
            if ($upload['ignore_tests']) {
                $exclude[] = 'tests';
            }
            rex_install_archive::copyDirToArchive(rex_path::addon($addonkey), $archive, null, $exclude);
            if ($upload['replace_assets']) {
                rex_install_archive::copyDirToArchive(rex_path::addonAssets($addonkey), $archive, $addonkey . '/files');
            }
            $file['checksum'] = md5_file($archive);
        }
        rex_install_webservice::post(rex_install_packages::getPath('?package=' . $addonkey . '&file_id=' . rex_request('file', 'int', 0)), array('file' => $file), $archive);

        if ($archive) {
            rex_file::delete($archive);
        }
        unset($_REQUEST['addonkey']);
        unset($_REQUEST['file']);
        rex_install_packages::deleteCache();

        return $I18N->msg('install_info_addon_uploaded', $addonkey);
    }
}
