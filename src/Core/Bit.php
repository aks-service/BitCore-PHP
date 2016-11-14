<?php
namespace Bit\Core;
use Bit\Vars\UUID;

use Bit\Enum\Lang;

use Bit\Core\Enum\Dev;
use Bit\Core\Enum\Mode;
use Bit\Core\Enum\Ring;
/**
 * Bit class.
 *
 * Bit implements a few fundamental static methods.
 *
 * To use the static methods, Use Bit as the class name rather than BitBase.
 * BitBase is meant to serve as the base class of Bit. The latter might be
 * rewritten for customization.
 *
 * @author      Bitcoding <bitcoding@bitcoding.eu>
 * @copyright   Copyright &copy; 2009-2016, Bitcoding
 * @link        http://www.lessphp.eu/
 * @link        http://www.bitcoding.eu/
 * @license     http://www.bitcoding.eu/license/
 *
 * @version     0.5.0 (newbread): Bit.php
 * @package     System
 */
class Bit
{
    /**
     * File extension for PHP files.
     */
    const PHP_EXT = 'php';

    /**
     * File extension for translations files.
     */
    const LANG_EXT = 'lng';

    /**
     * File extension for Template files.
     */
    const HTML_EXT = 'html';

    const BIT_RULE = '42495443-4f44-494e-475f-52554c450d0a';
    const BIT_CACHE = '42495443-4f44-494e-475f-52554c450d0a';
    const BIT_HMAC = '42495443-4f44-494e-475f-484d41430d0a';


    /**
     * @return string the version of Bit framework
     */
    public static function getVersion() {
        return '0.5.0';
    }
    /**
     * Return the class name namespaced. This method checks if the class is defined on the
     * application/plugin, otherwise try to load from the BitPHP core
     *
     * @param string $class Class name
     * @param string $type Type of class
     * @param string $suffix Class name suffix
     * @return bool|string False if the class is not found or namespaced class name
     */
    public static function className($class, $type = '', $suffix = '')
    {
        if (strpos($class, '\\') !== false) {
            return $class;
        }

        list($plugin, $name) = pluginSplit($class);
        $base = $plugin ?: Configure::read('App.namespace');
        $base = str_replace('/', '\\', rtrim($base, '\\'));
        $fullname = '\\' . str_replace('/', '\\', $type . '\\' . $name) . $suffix;

        if (static::_classExistsInBase($fullname, $base)) {
            return $base . $fullname;
        }
        if ($plugin) {
            return false;
        }
        if (static::_classExistsInBase($fullname, 'Bit')) {
            return 'Bit' . $fullname;
        }
        return false;
    }

    /**
     * _classExistsInBase
     *
     * Test isolation wrapper
     *
     * @param string $name Class name.
     * @param string $namespace Namespace.
     * @return bool
     */
    protected static function _classExistsInBase($name, $namespace)
    {
        return class_exists($namespace . $name);
    }

    /**
     * Used to read information stored path
     *
     * Usage:
     *
     * ```
     * Bit::path('Plugin');
     * ```
     *
     * Will return the configured paths for plugins. This is a simpler way to access
     * the `App.paths.plugins` configure variable.
     *
     * ```
     * Bit::path('Model/Datasource', 'MyPlugin');
     * ```
     *
     * Will return the path for datasources under the 'MyPlugin' plugin.
     *
     * @param string $type type of path
     * @param string|null $plugin name of plugin
     * @return array
     */
    public static function path($type, $plugin = null)
    {
        if ($type === 'Plugin') {
            return (array)Configure::read('App.paths.plugins');
        }
        if ($type === 'PHPQuery') {
            return (array)Configure::read('App.paths.phpquery');
        }
        if (empty($plugin) && $type === 'Locale') {
            return (array)Configure::read('App.paths.locales');
        }
        if (empty($plugin) && $type === 'Template') {
            return (array)Configure::read('App.paths.templates');
        }
        if (!empty($plugin)) {
            return [Plugin::classPath($plugin) . $type . DIRECTORY_SEPARATOR];
        }
        return [APP . $type . DIRECTORY_SEPARATOR];
    }
    /**
     * Returns the full path to a package inside the BitPHP core
     *
     * Usage:
     *
     * `Bit::core('Cache/Engine');`
     *
     * Will return the full path to the cache engines package.
     *
     * @param string $type Package type.
     * @return array Full path to package
     */
    public static function core($type) {
        return [BIT . str_replace('/', DS, $type) . DS];
    }



    //OLD Implement

    /**
     * Returns a list of user preferred languages.
     * The languages are returned as an array. Each array element
     * represents a single language preference. The languages are ordered
     * according to user preferences. The first language is the most preferred.
     * @return array list of user preferred languages.
     */
    public static function getUserLanguages()
    {
        static $languages = null;
        if ($languages === null) {
            if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
                $languages[0] = Lang::EN;
            else {
                $languages = array();
                foreach (explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $language) {
                    $array = explode(';q=', trim($language));
                    $languages[trim($array[0])] = isset($array[1]) ? (float)$array[1] : 1.0;
                }
                arsort($languages);
                $languages = array_keys($languages);
                if (empty($languages))
                    $languages[0] = Lang::EN;
            }
        }
        return $languages;
    }

    /**
     * Returns the most preferred language by the client user.
     * @return string the most preferred language by the client user, defaults to English.
     */
    public static function getPreferredLanguage()
    {
        static $language = null;
        if (isset($_SESSION['BitLanguage'])) {
            return $_SESSION['BitLanguage'];
        }
        if ($language === null) {
            $langs = Bit::getUserLanguages();
            $lang = explode('-', $langs[0]);
            if (empty($lang[0]) || !ctype_alpha($lang[0])) {
                $language = Lang::EN;
            } elseif ($lang[0] == 'de') {
                $language = Lang::DE;
            } else {
                $language = Lang::EN;
            }
        }
        return $language;
    }

    /**
     * @param $string
     * @param string $base
     * @return bool
     */
    public static function getUUIDv5($string, $base = self::BIT_RULE)
    {
        return UUID::v5($base, $string);
    }

    public static function getSysTemp()
    {
        return sys_get_temp_dir();
    }

    public static function getFileHash($file, $mode = 'md5', $secret = self::BIT_HMAC)
    {
        return hash_hmac_file($mode, $file, $secret);
    }

    public static function getIP()
    {
        return PROXY_MODE && Vars::server_isip('HTTP_X_FORWARDED_FOR') ? Vars::server_getstring('HTTP_X_FORWARDED_FOR') : $_SERVER["REMOTE_ADDR"];
    }

}
