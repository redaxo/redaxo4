<?php

/**
 * @package redaxo\install
 */
abstract class rex_api_install_package_download
{
    protected $addonkey;
    protected $fileId;
    protected $file;
    protected $archive;

    public function execute()
    {
        global $I18N;

        $this->addonkey = rex_request('addonkey', 'string');
        $function = static::GET_PACKAGES_FUNCTION;
        $packages = rex_install_packages::$function();
        $this->fileId = rex_request('file', 'int');
        if (!isset($packages[$this->addonkey]['files'][$this->fileId])) {
            return null;
        }
        $this->file = $packages[$this->addonkey]['files'][$this->fileId];
        $this->checkPreConditions();
        $archivefile = rex_install_webservice::getArchive($this->file['path']);
        $message = '';
        $this->archive = $archivefile;
        if ($this->file['checksum'] != md5_file($archivefile)) {
            $message = $I18N->msg('install_warning_zip_wrong_checksum');
        } elseif (!file_exists("phar://$archivefile/" . $this->addonkey)) {
            $message = $I18N->msg('install_warning_zip_wrong_format');
        } elseif (is_string($msg = $this->doAction())) {
            $message = $msg;
        }
        rex_file::delete($archivefile);
        if ($message) {
            $message = $I18N->msg('install_warning_addon_not_' . static::VERB, $this->addonkey) . '<br />' . $message;
            $success = false;
        } else {
            $message = $I18N->msg('install_info_addon_' . static::VERB, $this->addonkey)
                             . (static::SHOW_LINK ? ' <a href="index.php?page=addon">' . $I18N->msg('install_to_addon_page') . '</a>' : '');
            $success = true;
            unset($_REQUEST['addonkey']);
        }
        if ($success) {
            return $message;
        } else {
            throw new rex_install_functional_exception($message);
        }
    }

    protected function extractArchiveTo($dir)
    {
        global $I18N;

        if (!rex_install_archive::extract($this->archive, $dir, $this->addonkey)) {
            rex_dir::delete($dir);
            return $I18N->msg('install_warning_addon_zip_not_extracted');
        }
        return true;
    }

    abstract protected function checkPreConditions();

    abstract protected function doAction();
}
