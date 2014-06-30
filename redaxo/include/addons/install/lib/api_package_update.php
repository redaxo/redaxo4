<?php

/**
 * @package redaxo\install
 */
class rex_api_install_package_update extends rex_api_install_package_download
{
    const GET_PACKAGES_FUNCTION = 'getUpdatePackages';
    const VERB = 'updated';
    const SHOW_LINK = false;

    protected function checkPreConditions()
    {
        if (!OOAddon::isAvailable($this->addonkey)) {
            throw new rex_install_functional_exception(sprintf('AddOn "%s" is not available!', $this->addonkey));
        }
        if (!rex_string::versionCompare($this->file['version'], OOAddon::getVersion($this->addonkey), '>')) {
            throw new rex_install_functional_exception(sprintf('Existing version of AddOn "%s" (%s) is newer than %s', $this->addonkey, OOAddon::getVersion($this->addonkey), $this->file['version']));
        }
    }

    public function doAction()
    {
        global $I18N;

        $path = rex_path::addon($this->addonkey);
        $temppath = rex_path::addon('.new.' . $this->addonkey);

        if (($msg = $this->extractArchiveTo($temppath)) !== true) {
            return $msg;
        }

        // ---- include update.php
        if (file_exists($temppath . 'update.inc.php')) {
            try {
                require $temppath . 'update.inc.php';
            } catch (rex_install_functional_exception $e) {
                return $e->getMessage();
            }
            if (($msg = OOAddon::getProperty($this->addonkey, 'updatemsg', '')) != '') {
                return $msg;
            }
            if (!OOAddon::getProperty($this->addonkey, 'update', true)) {
                return $I18N->msg('package_no_reason');
            }
        }

        // ---- backup
        $assets = rex_path::addonAssets($this->addonkey);
        if (OOAddon::getProperty('install', 'backups')) {
            $archivePath = rex_path::addonData('install', $this->addonkey . '/');
            rex_dir::create($archivePath);
            $archive = $archivePath . strtolower(preg_replace('/[^a-z0-9-_.]/i', '_', OOAddon::getVersion($this->addonkey))) . '.zip';
            rex_install_archive::copyDirToArchive($path, $archive);
            if (is_dir($assets)) {
                rex_install_archive::copyDirToArchive($assets, $archive, 'assets');
            }
        }

        // ---- copy plugins to new addon dir
        foreach (OOPlugin::getRegisteredPlugins($this->addonkey) as $plugin) {
            $pluginPath = $temppath . '/plugins/' . $plugin;
            if (!is_dir($pluginPath)) {
                rex_dir::copy(rex_path::plugin($this->addonkey, $plugin), $pluginPath);
            } elseif (OOPlugin::isInstalled($this->addonkey, $plugin) && is_dir($pluginPath . '/files')) {
                rex_dir::copy($pluginPath . '/files', rex_path::pluginAssets($this->addonkey, $plugin));
            }
        }

        // ---- update main addon dir
        rex_dir::delete($path);
        rename($temppath, $path);

        // ---- update assets
        $origAssets = rex_path::addon($this->addonkey, 'assets');
        if (is_dir($origAssets)) {
            rex_dir::copy($origAssets, $assets);
        }

        OOAddon::setProperty($this->addonkey, 'version', $this->file['version']);
        rex_install_packages::updatedPackage($this->addonkey, $this->fileId);
    }

    public function __destruct()
    {
        rex_dir::delete(rex_path::addon('.new.' . $this->addonkey));
    }
}
