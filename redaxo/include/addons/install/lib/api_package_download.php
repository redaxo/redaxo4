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
        $checksum = md5_file($archivefile);

        if (class_exists("ZipArchive")) {
            $success = false;
            $zip = new ZipArchive;
            if ($zip->open($archivefile) === TRUE) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    if (substr($filename,0,strlen($this->addonkey.'/')) != $this->addonkey.'/') {
                        $zip->deleteIndex($i);
                    } else {
                        $success = true;
                    }
                }
                $zip->close();
            }

            if (!$success) {
                $message = $I18N->msg('install_warning_zip_wrong_format');
            }

        } else if (!file_exists("phar://$archivefile/" . $this->addonkey)) {
            $message = $I18N->msg('install_warning_zip_wrong_format');
        }

        if ($message != "") {
        } else if ($this->file['checksum'] != $checksum) {
            $message = $I18N->msg('install_warning_zip_wrong_checksum');
        } else if (is_string($msg = $this->doAction())) {
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
