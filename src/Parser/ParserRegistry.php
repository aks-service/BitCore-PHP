<?php
namespace Bit\Parser;

use BadMethodCallException;
use Bit\Core\Bit;
use Bit\Core\ObjectRegistry;
use RuntimeException;

/**
 * An object registry for cache engines.
 *
 * Used by \Bit\Parser\Parser to load and manage parser engines.
 */
class ParserRegistry extends ObjectRegistry
{

    /**
     * Resolve a cache engine classname.
     *
     * Part of the template method for Bit\Core\ObjectRegistry::load()
     *
     * @param string $class Partial classname to resolve.
     * @return string|false Either the correct classname or false.
     */
    protected function _resolveClassName($class)
    {
        if (is_object($class)) {
            return $class;
        }
        return Bit::className($class, 'Parser/Engine', 'Engine');
    }

    /**
     * Throws an exception when a cache engine is missing.
     *
     * Part of the template method for Bit\Core\ObjectRegistry::load()
     *
     * @param string $class The classname that is missing.
     * @param string $plugin The plugin the cache is missing in.
     * @return void
     * @throws \BadMethodCallException
     */
    protected function _throwMissingClassError($class, $plugin)
    {
        throw new BadMethodCallException(sprintf('Parser engine %s is not available.', $class));
    }

    /**
     * Create the cache engine instance.
     *
     * Part of the template method for Bit\Core\ObjectRegistry::load()
     *
     * @param string|\Bit\Cache\CacheEngine $class The classname or object to make.
     * @param string $alias The alias of the object.
     * @param array $config An array of settings to use for the cache engine.
     * @return \Bit\Parser\ParserEngine The constructed CacheEngine class.
     * @throws \RuntimeException when an object doesn't implement the correct interface.
     */
    protected function _create($class, $alias, $config)
    {
        if (is_object($class)) {
            $instance = $class;
        }

        unset($config['className']);
        if (!isset($instance)) {
            $instance = new $class($config);
        }

        if (!($instance instanceof ParserEngine)) {
            throw new RuntimeException(
                'Parser engines must use Parser\ParserEngine as a base class.'
            );
        }

        if (!$instance->init($config)) {
            throw new RuntimeException(
                sprintf('Parser engine %s is not properly configured.', get_class($instance))
            );
        }
        
        return $instance;
    }

    /**
     * Remove a single adapter from the registry.
     *
     * @param string $name The adapter name.
     * @return void
     */
    public function unload($name)
    {
        unset($this->_loaded[$name]);
    }
}
