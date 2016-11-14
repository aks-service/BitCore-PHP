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

    public static function selector($selector = null){
        if($selector === null){
            if(!static::$_selector)
                static::$_selector = new CssSelectorConverter(true);
            return static::$_selector;
        }

        return static::$_selector = $selector;
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

    /**
     * Multi-purpose function.
     * Use pq() as shortcut.
     *
     * @param string|DOMNode|DOMNodeList|array	$arg1	HTML markup, CSS Selector, DOMNode or array of DOMNodes
     * @param string|phpQueryObject|DOMNode	$context	DOM ID from $pq->getDocumentID(), phpQuery object (determines also query root) or DOMNode (determines also query root)
     *
     * @return phpQueryObject|QueryTemplatesSource|QueryTemplatesParse|QueryTemplatesSourceQuery|QueryTemplatesPhpQuery|false
     * phpQuery object or false in case of error.
     */
    public function pq($arg1, $context = null) {
        /*$domId = (is_null($context)) ? (is_object($arg1) && !($arg1 instanceof phpQueryObject) ? self::getDocumentID($arg1): self::$defaultDocumentID) : self::getDocumentID($context) ;

        $phpQuery = new phpQueryObject($domId);

        if ($arg1 instanceof phpQueryObject) {
            if ($arg1->getDocumentID() == $domId)
                return $arg1;

            $phpQuery->elements = array();
            foreach ($arg1->elements as $node)
                $phpQuery->elements[] = $phpQuery->document->importNode($node, true);
            return $phpQuery;
        } else if ($arg1 instanceof DOMNODE || (is_array($arg1) && isset($arg1[0]) && $arg1[0] instanceof DOMNODE)) {
            if (!($arg1 instanceof DOMNODELIST) && !is_array($arg1))
                $arg1 = array($arg1);

            $phpQuery->elements = array();
            foreach ($arg1 as $node) {
                $sameDocument = $node->ownerDocument instanceof DOMDOCUMENT && !$node->ownerDocument->isSameNode($phpQuery->document);
                $phpQuery->elements[] = $sameDocument ? $phpQuery->document->importNode($node, true) : $node;
            }
            return $phpQuery;
        } else if (self::isMarkup($arg1)) {
            return $phpQuery->newInstance(
                $phpQuery->documentWrapper->import($arg1)
            );
        } else {
            if ($context){
                if($context instanceof phpQueryObject){
                    $phpQuery->elements = $context->elements;
                }else if ($context instanceof DOMNODELIST) {
                    $phpQuery->elements = array();
                    foreach ($context as $node)
                        $phpQuery->elements[] = $node;
                } else if ($context instanceof DOMNODE)
                    $phpQuery->elements = array($context);
            }
            return $phpQuery->find($arg1);
        }*/
    }
    function __debugInfo()
    {
        return [
            'methods' => static::$_methods
        ];
        // TODO: Implement __debugInfo() method.
    }
}






