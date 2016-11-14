<?php
namespace Bit\Controller;

use Bit\Controller\Exception\MissingComponentException;
use Bit\Core\Bit;
use Bit\Core\ObjectRegistry;
use Bit\Event\EventDispatcherInterface;
use Bit\Event\EventDispatcherTrait;

/**
 * ComponentRegistry is a registry for loaded components
 *
 * Handles loading, constructing and binding events for component class objects.
 */
class ComponentRegistry extends ObjectRegistry implements EventDispatcherInterface
{

    use EventDispatcherTrait;

    /**
     * The controller that this collection was initialized with.
     *
     * @var \Bit\Controller\Controller
     */
    protected $_Controller = null;

    /**
     * Constructor.
     *
     * @param \Bit\Controller\Controller|null $controller Controller instance.
     */
    public function __construct(Controller $controller = null)
    {
        if ($controller) {
            $this->setController($controller);
        }
    }

    /**
     * Get the controller associated with the collection.
     *
     * @return \Bit\Controller\Controller Controller instance
     */
    public function getController()
    {
        return $this->_Controller;
    }

    /**
     * Set the controller associated with the collection.
     *
     * @param \Bit\Controller\Controller $controller Controller instance.
     * @return void
     */
    public function setController(Controller $controller)
    {
        $this->_Controller = $controller;
        $this->eventManager($controller->eventManager());
    }

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
        return Bit::className($class, 'Controller/Component', 'Component');
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
        throw new MissingComponentException([
            'class' => $class . 'Component',
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
     * @return \Bit\Controller\Component The constructed component class.
     */
    protected function _create($class, $alias, $config)
    {
        $instance = new $class($this, $config);
        $enable = isset($config['enabled']) ? $config['enabled'] : true;
        if ($enable) {
            $this->eventManager()->on($instance);
        }
        return $instance;
    }
}
