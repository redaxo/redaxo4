<?php

/**
 * @package redaxo\install
 */
class rex_api_install_package_delete
{
    public function execute()
    {
        global $I18N;

        $addonkey = rex_request('addonkey', 'string');
        rex_install_webservice::delete(rex_install_packages::getPath('?package=' . $addonkey . '&file_id=' . rex_request('file', 'int', 0)));

        unset($_REQUEST['addonkey']);
        unset($_REQUEST['file']);
        rex_install_packages::deleteCache();

        return $I18N->msg('install_info_addon_deleted', $addonkey);
    }
}
