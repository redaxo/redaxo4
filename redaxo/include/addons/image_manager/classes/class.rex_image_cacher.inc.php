<?php

class rex_image_cacher
{
    var $cache_path;

    function rex_image_cacher($cache_path)
    {
        global $REX;

        $this->cache_path = $cache_path;
    }

    /*public*/ function isCached(/*rex_image*/ $image, $cacheParams)
    {

        if (!rex_image::isValid($image)) {
            trigger_error('Given image is not a valid rex_image', E_USER_ERROR);
        }

        $original_cache_file = $this->getCacheFile($image, $cacheParams);
        $cache_files = glob($original_cache_file . '*');

        // ----- check for cache file
        if (is_array($cache_files) && count($cache_files) == 1) {
            $cache_file = $cache_files[0];

            // time of cache
            $cachetime = filectime($cache_file);
            $imagepath = $image->getFilePath();

            if ($original_cache_file != $cache_file) {
                $image->img['format'] = strtoupper(OOMedia::_getExtension($cache_file));
                $image->img['file'] = $image->img['file'] . '.' . OOMedia::_getExtension($cache_file);

            }

            // file exists?
            if (file_exists($imagepath)) {
                $filetime = filectime($imagepath);

            } else {
                // Missing original file for cache-validation!
                $image->sendErrorImage();

            }
            // cache is newer?
            if ($cachetime > $filetime) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns a rex_image instance representing the cached image.
     * This Method requires a already cached file.
     *
     * Use rex_image_manager::getImageCache() if the cache should be created if needed.
     */
    /*public*/ function getCachedImage($filename, $cacheParams)
    {
        $cacheFile = $this->_getCacheFile($filename, $cacheParams);
        $rex_image = new rex_image($cacheFile);
        $rex_image->prepare();
        return $rex_image;
    }

    /*public*/ function getCacheFile(/*rex_image*/ $image, $cacheParams)
    {
        return $this->_getCacheFile($image->getFileName(), $cacheParams);
    }

    /*protected*/ function _getCacheFile($filename, $cacheParams)
    {
        if (!is_string($cacheParams)) {
            $cacheParams = md5(serialize($cacheParams));
        }
        return $this->cache_path . 'image_manager__' . $cacheParams . '_' . $filename;
    }

    /*public*/ function sendImage(/*rex_image*/ $image, $cacheParams, $lastModified = null)
    {
        global $REX;

        if (!rex_image::isValid($image)) {
            trigger_error('Given image is not a valid rex_image', E_USER_ERROR);
        }

        $cacheFile = $this->getCacheFile($image, $cacheParams);

        // save image to file
        if (!$this->isCached($image, $cacheParams)) {
            $image->prepare();
            $image->save($cacheFile);
        }

        $tmp = $REX['USE_GZIP'];
        $REX['USE_GZIP'] = 'false';

            // send file
        $image->sendHeader();
        $format = $image->getFormat() == 'JPG' ? 'jpeg' : strtolower($image->getFormat());
        rex_send_file($cacheFile, 'image/' . $format, 'frontend');

        $REX['USE_GZIP'] = $tmp;

    }

    /*
     * Static Method: Returns True, if the given cacher is a valid rex_image_cacher
     */
    static /*public*/ function isValid($cacher)
    {
        return is_object($cacher) && is_a($cacher, 'rex_image_cacher');
    }

    /**
     * deletes all cache files for the given filename.
     * if not filename is provided all cache files are cleared.
     *
     * Returns the number of cachefiles which have been removed.
     *
     * @param $filename
     */
    static function deleteCache($filename = '', $cacheParams = null)
    {
        global $REX;

        $filename .= '*';

        if (!$cacheParams) {
            $cacheParams = '*';
        }

        $folders = array();
        $folders[] = $REX['GENERATED_PATH'] . '/files/';

        $counter = 0;
        foreach ($folders as $folder) {

            $glob = glob($folder . 'image_manager__' . $cacheParams . '_' . $filename);
            if ($glob) {
                foreach ($glob as $file) {
                    if (is_file($file) && unlink($file)) {
                        $counter++;
                    }
                }
            }
        }

        return $counter;
    }
}
