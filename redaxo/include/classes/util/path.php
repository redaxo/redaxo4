<?php

/**
 * Utility class to generate absolute paths
 *
 * @author gharlan
 *
 * @package redaxo\core
 */
class rex_path
{
    protected static $backend = 'redaxo';

    /**
     * Returns a base path
     *
     * @param string $file File
     * @return string
     */
    public static function base($file = '')
    {
        global $REX;
        return self::normalize($REX['FRONTEND_PATH'] . '/' . $file);
    }

    /**
     * Returns the path to the frontend
     *
     * @param string $file File
     * @return string
     */
    public static function frontend($file = '')
    {
        return self::base($file);
    }

    /**
     * Returns the path to the frontend-controller (index.php from frontend)
     *
     * @return string
     */
    public static function frontendController()
    {
        global $REX;
        return self::base($REX['FRONTEND_FILE']);
    }

    /**
     * Returns the path to the backend
     *
     * @param string $file File
     * @return string
     */
    public static function backend($file = '')
    {
        return self::base(self::$backend . '/' . $file);
    }

    /**
     * Returns the path to the backend-controller (index.php from backend)
     *
     * @return string
     */
    public static function backendController()
    {
        return self::backend('index.php');
    }

    /**
     * Returns the path to the media-folder
     *
     * @param string $file File
     * @return string
     */
    public static function media($file = '')
    {
        global $REX;
        return self::base($REX['MEDIA_DIR'] . '/' . $file);
    }

    /**
     * Returns the path to the assets folder of the core, which contains all assets required by the core to work properly.
     *
     * @param string $file File
     * @return string
     */
    public static function assets($file = '')
    {
        return self::media($file);
    }

    /**
     * Returns the path to the assets folder of the given addon, which contains all assets required by the addon to work properly.
     *
     * @param string $addon Addon
     * @param string $file  File
     * @return string
     *
     * @see assets()
     */
    public static function addonAssets($addon, $file = '')
    {
        global $REX;
        return self::base($REX['MEDIA_ADDON_DIR'] . '/' . $addon . '/' . $file);
    }

    /**
     * Returns the path to the assets folder of the given plugin of the given addon
     *
     * @param string $addon  Addon
     * @param string $plugin Plugin
     * @param string $file   File
     * @return string
     *
     * @see assets()
     */
    public static function pluginAssets($addon, $plugin, $file = '')
    {
        return self::addonAssets($addon, 'plugins/' . $plugin . '/' . $file);
    }

    /**
     * Returns the path to the data folder of the core.
     *
     * @param string $file File
     * @return string
     */
    public static function data($file = '')
    {
        return self::src('data/' . $file);
    }

    /**
     * Returns the path to the data folder of the given addon.
     *
     * @param string $addon Addon
     * @param string $file  File
     * @return string
     */
    public static function addonData($addon, $file = '')
    {
        return self::data('addons/' . $addon . '/' . $file);
    }

    /**
     * Returns the path to the data folder of the given plugin of the given addon.
     *
     * @param string $addon  Addon
     * @param string $plugin Plugin
     * @param string $file   File
     * @return string
     */
    public static function pluginData($addon, $plugin, $file = '')
    {
        return self::addonData($addon, 'plugins/' . $plugin . '/' . $file);
    }

    /**
     * Returns the path to the cache folder of the core
     *
     * @param string $file File
     * @return string
     */
    public static function cache($file = '')
    {
        global $REX;
        return self::normalize($REX['GENERATED_PATH'] . '/' . $file);
    }

    /**
     * Returns the path to the cache folder of the given addon.
     *
     * @param string $addon Addon
     * @param string $file  File
     * @return string
     */
    public static function addonCache($addon, $file = '')
    {
        return self::cache('addons/' . $addon . '/' . $file);
    }

    /**
     * Returns the path to the cache folder of the given plugin
     *
     * @param string $addon  Addon
     * @param string $plugin Plugin
     * @param string $file   File
     * @return string
     */
    public static function pluginCache($addon, $plugin, $file = '')
    {
        return self::addonCache($addon, 'plugins/' . $plugin . '/' . $file);
    }

    /**
     * Returns the path to the src folder.
     *
     * @param string $file File
     * @return string
     */
    public static function src($file = '')
    {
        global $REX;
        return self::normalize($REX['INCLUDE_PATH'] . '/' . $file);
    }

    /**
     * Returns the path to the actual core
     *
     * @param string $file File
     * @return string
     */
    public static function core($file = '')
    {
        return self::src($file);
    }

    /**
     * Returns the base path to the folder of the given addon
     *
     * @param string $addon Addon
     * @param string $file  File
     * @return string
     */
    public static function addon($addon, $file = '')
    {
        return self::src('addons/' . $addon . '/' . $file);
    }

    /**
     * Returns the base path to the folder of the plugin of the given addon
     *
     * @param string $addon  Addon
     * @param string $plugin Plugin
     * @param string $file   File
     * @return string
     */
    public static function plugin($addon, $plugin, $file = '')
    {
        return self::addon($addon, 'plugins/' . $plugin . '/' . $file);
    }

    protected static function normalize($path)
    {
        return strtr($path, '/\\', DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR);
    }
}
