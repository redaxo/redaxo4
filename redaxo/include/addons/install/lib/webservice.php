<?php

/**
 * @package redaxo\install
 */
class rex_install_webservice
{
    const HOST = 'www.redaxo.org';
    const PORT = 443;
    const SSL = true;
    const PATH = '/de/ws/';
    const REFRESH_CACHE = 600;

    private static $cache;

    public static function getJson($path)
    {
        global $I18N;

        if (is_array($cache = self::getCache($path))) {
            return $cache;
        }
        $fullpath = self::PATH . self::getPath($path);

        $error = null;
        try {
            $socket = rex_socket::factory(self::HOST, self::PORT, self::SSL);
            $socket->setPath($fullpath);
            $response = $socket->doGet();
            if ($response->isOk()) {
                $data = json_decode($response->getBody(), true);
                if (isset($data['error']) && is_string($data['error'])) {
                    $error = $I18N->msg('install_webservice_error') . '<br />' . $data['error'];
                } elseif (is_array($data)) {
                    self::setCache($path, $data);
                    return $data;
                }
            }
        } catch (rex_socket_exception $e) {
        }

        if (!$error) {
            $error = $I18N->msg('install_webservice_unreachable');
        }

        throw new rex_install_functional_exception($error);
    }

    public static function getArchive($url)
    {
        global $I18N;

        try {
            $socket = rex_socket::factoryUrl($url);
            $response = $socket->doGet();
            if ($response->isOk()) {
                $filename = basename($url);
                $file = rex_path::addonCache('install', md5($filename) . '.' . rex_file::extension($filename));
                $response->writeBodyTo($file);
                return $file;
            }
        } catch (rex_socket_exception $e) {
        }

        throw new rex_install_functional_exception($I18N->msg('install_archive_unreachable'));
    }

    public static function post($path, array $data, $archive = null)
    {
        global $I18N;

        $fullpath = self::PATH . self::getPath($path);
        $error = null;
        try {
            $socket = rex_socket::factory(self::HOST, self::PORT, self::SSL);
            $socket->setPath($fullpath);
            $files = array();
            if ($archive) {
                $files['archive']['path'] = $archive;
                $files['archive']['type'] = 'application/zip';
            }
            $response = $socket->doPost($data, $files);
            if ($response->isOk()) {
                $data = json_decode($response->getBody(), true);
                if (!isset($data['error']) || !is_string($data['error'])) {
                    return;
                }
                $error = $I18N->msg('install_webservice_error') . '<br />' . $data['error'];
            }
        } catch (rex_socket_exception $e) {
        }

        if (!$error) {
            $error = $I18N->msg('install_webservice_unreachable');
        }

        throw new rex_install_functional_exception($error);
    }

    public static function delete($path)
    {
        global $I18N;

        $fullpath = self::PATH . self::getPath($path);
        $error = null;
        try {
            $socket = rex_socket::factory(self::HOST, self::PORT, self::SSL);
            $socket->setPath($fullpath);
            $response = $socket->doDelete();
            if ($response->isOk()) {
                $data = json_decode($response->getBody(), true);
                if (!isset($data['error']) || !is_string($data['error'])) {
                    return;
                }
                $error = $I18N->msg('install_webservice_error') . '<br />' . $data['error'];
            }
        } catch (rex_socket_exception $e) {
        }

        if (!$error) {
            $error = $I18N->msg('install_webservice_unreachable');
        }

        throw new rex_install_functional_exception($error);
    }

    private static function getPath($path)
    {
        global $REX;

        $path = strpos($path, '?') === false ? rtrim($path, '/') . '/?' : $path . '&';
        $path .= 'rex_version=' . $REX['VERSION'] . '.' . $REX['SUBVERSION'];
        if (OOAddon::getProperty('install', 'api_login')) {
            $path .= '&api_login=' . OOAddon::getProperty('install', 'api_login') . '&api_key=' . OOAddon::getProperty('install', 'api_key');
        }
        return $path;
    }

    public static function deleteCache($pathBegin = null)
    {
        self::loadCache();
        if ($pathBegin) {
            foreach (self::$cache as $path => $cache) {
                if (strpos($path, $pathBegin) === 0) {
                    unset(self::$cache[$path]);
                }
            }
        } else {
            self::$cache = array();
        }
        rex_file::putCache(rex_path::addonCache('install', 'webservice.cache'), self::$cache);
    }

    private static function getCache($path)
    {
        self::loadCache();
        if (isset(self::$cache[$path])) {
            return self::$cache[$path]['data'];
        }
        return null;
    }

    private static function loadCache()
    {
        if (self::$cache === null) {
            foreach ((array) rex_file::getCache(rex_path::addonCache('install', 'webservice.cache')) as $path => $cache) {
                if ($cache['stamp'] > time() - self::REFRESH_CACHE) {
                    self::$cache[$path] = $cache;
                }
            }
        }
    }

    private static function setCache($path, $data)
    {
        self::$cache[$path]['stamp'] = time();
        self::$cache[$path]['data'] = $data;
        rex_file::putCache(rex_path::addonCache('install', 'webservice.cache'), self::$cache);
    }
}
