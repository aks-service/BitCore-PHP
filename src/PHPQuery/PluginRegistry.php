<?php
namespace Bit\PHPQuery;

use Bit\Controller\Exception\MissingComponentException;
use Bit\Core\Bit;
use Bit\Core\ObjectRegistry;
use Bit\Event\EventDispatcherInterface;
use Bit\Event\EventDispatcherTrait;

use Bit\PHPQuery\Exception\MissingPhpQueryPluginException;

/**
 * ComponentRegistry is a registry for loaded components
 *
 * Handles loading, constructing and binding events for component class objects.
 */
class PluginRegistry extends ObjectRegistry 
{

    /**
     * Resolve a component classname.
     *
     * Part of the template method for Bit\Core\ObjectRegistry::load()
     *
     * @param string $class Partial classname to resolve.
     * @return string|false Either the correct classname or false.
     */
    protected function _resolveClassName($class)
    {
        return Bit::className($class, 'PHPQuery/Plugin', 'Plugin');
    }

    /**
     * Throws an exception when a component is missing.
     *
     * Part of the template method for Bit\Core\ObjectRegistry::load()
     *
     * @param string $class The classname that is missing.
     * @param string $plugin The plugin the component is missing in.
     * @return void
     * @throws \Bit\Controller\Exception\MissingComponentException
     */
    protected function _throwMissingClassError($class, $plugin)
    {
        throw new MissingPhpQueryPluginException([
            'class' => $class . 'Plugin',
            'plugin' => $plugin
        ]);
    }

    /**
     * Create the component instance.
     *
     * Part of the template method for Bit\Core\ObjectRegistry::load()
     * Enabled components will be registered with the event manager.
     *
     * @param string $class The classname to create.
     * @param string $alias The alias of the component.
     * @param array $config An array of config to use for the component.
     * @return \Bit\PHPQuery\Plugin The constructed component class.
     */
    protected function _create($class, $alias, $config)
    {
        $instance = new $class($config);
        return $instance;
    }
}
