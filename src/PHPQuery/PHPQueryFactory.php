<?php
namespace Bit\PHPQuery;

use Bit\Core\Bit;
use Bit\Core\Configure;
use Bit\PHPQuery\Exception\MissingFunctionException;
use Bit\PHPQuery\Exception\ExistMethodException;

use Bit\PHPQuery\Exception\MissingPluginException;
use Symfony\Component\CssSelector\CssSelectorConverter;

class PHPQueryFactory{

    /**
     * Holds a list of all loaded plugins and their configuration
     *
     * @var \Symfony\Component\CssSelector\CssSelectorConverter|null
     */
    protected static $_selector = null;

    /**
     * Holds a list of all loaded plugins and their configuration
     *
     * @var string[]
     */
    protected static $_selectorCache = null;

    /**
     * @param null $selector
     * @return null|CssSelectorConverter
     */
    public static function selector($selector = null){
        if($selector === null){
            if(!static::$_selector)
                static::$_selector = new CssSelectorConverter(true);
            return static::$_selector;
        }

        return static::$_selector = $selector;
    }

    /**
     * @param $selector
     * @return string
     */
    public static function toXPath($selector){
        if(isset(static::$_selectorCache[$selector])){
            return static::$_selectorCache[$selector];
        }

        return static::$_selectorCache[$selector] = static::selector()->toXPath($selector);
    }

    /**
     * Holds a list of all loaded plugins and their configuration
     *
     * @var array
     */
    protected static $_plugins = [];


    /**
     * Get the component registry for this controller.
     *
     * @param \Bit\PHPQuery\PluginRegistry|null $components Plugin registry.
     *
     * @return \Bit\PHPQuery\PluginRegistry
     */
    public static function plugins($_plugins = null)
    {
        static $plugins = null;
        if ($_plugins === null && $plugins === null) {
            $plugins = new PluginRegistry();
        }
        if ($_plugins !== null) {
            $plugins = $_plugins;
        }
        return $plugins;
    }

    /**
     * Get the component registry for this controller.
     *
     * @param string|null $_plugin Plugin
     *
     * @return \Bit\PHPQuery\Plugin
     */
    public static function plugin($_plugin)
    {
        return static::plugins()->$_plugin;
    }

    /**
     * Loads a plugin and optionally loads bootstrapping,
     * routing files or runs an initialization function.
     *
     */
    public static function load($plugin, array $config = [])
    {
        if (is_array($plugin)) {
            foreach ($plugin as $name => $conf) {
                list($name, $conf) = (is_numeric($name)) ? [$conf, $config] : [$name, $conf];
                static::plugin($name, $conf);
            }
            return;
        }
        //static::_loadConfig();

        static::plugins()->load($plugin,$config);

        return;
    }

    
    
    /**
     * Load the plugin path configuration file.
     *
     * @return void
     */
    protected static function _loadConfig()
    {
        if (Configure::check('phpquery.plugins')) {
            return;
        }

        $vendorFile = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'bit-phpquery-plugins.php';
        if (!file_exists($vendorFile)) {
            $vendorFile = dirname(dirname(dirname(dirname(__DIR__)))) . DIRECTORY_SEPARATOR . 'bit-phpquery-plugins.php';
            if (!file_exists($vendorFile)) {
                Configure::write(['phpquery.plugins' => []]);
                return;
            }
        }

        $config = require $vendorFile;
        Configure::write($config);
    }

    /**
     * Returns the filesystem path for a plugin
     *
     * @param string $plugin name of the plugin in CamelCase format
     * @return string path to the plugin folder
     * @throws \Bit\Core\Exception\MissingPluginException if the folder for plugin was not found or plugin has not been loaded
     */
    public static function path($plugin)
    {
        if (empty(static::$_plugins[$plugin])) {
            throw new MissingPluginException(['plugin' => $plugin]);
        }
        return static::$_plugins[$plugin]['path'];
    }

    /**
     * Returns the filesystem path for plugin's folder containing class folders.
     *
     * @param string $plugin name of the plugin in CamelCase format.
     * @return string Path to the plugin folder container class folders.
     * @throws \Bit\Core\Exception\MissingPluginException If plugin has not been loaded.
     */
    public static function classPath($plugin)
    {
        if (empty(static::$_plugins[$plugin])) {
            throw new MissingPluginException(['plugin' => $plugin]);
        }
        return static::$_plugins[$plugin]['classPath'];
    }
    /**
     * Holds a list of all loaded plugins and their configuration
     * PHPQueryFactory::method([
     *    'test'=> function(QueryObject $nodes,$attr = null, $value = null) {
     *             }
     * ]);
     *
     * @var array
     */
    protected static $_methods = [];

    public static function method($method = null,$call = null,$overwrite = false){
        if($call === null && is_string($method))
            return isset(static::$_methods[$method]) ? static::$_methods[$method] : null;

        if (is_array($method)) {
            foreach ($method as $name => $c) {
                static::method($name, $c,$overwrite);
            }
            return null;
        }
        
        if(!is_callable($call))
            throw new MissingFunctionException($method);

        if(!$overwrite && isset(static::$_methods[$method]))
            throw new ExistMethodException($method);

        static::$_methods[$method] = $call;
    }

    function __debugInfo()
    {
        return [
            'methods' => static::$_methods
        ];
        // TODO: Implement __debugInfo() method.
    }
}






