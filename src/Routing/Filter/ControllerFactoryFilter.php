<?php
namespace Bit\Routing\Filter;

use Bit\Core\Bit;
use Bit\Event\Event;
use Bit\Routing\DispatcherFilter;
use Bit\Utility\Inflector;
use ReflectionClass;

/**
 * A dispatcher filter that builds the controller to dispatch
 * in the request.
 *
 * This filter resolves the request parameters into a controller
 * instance and attaches it to the event object.
 */
class ControllerFactoryFilter extends DispatcherFilter
{

    /**
     * Priority is set high to allow other filters to be called first.
     *
     * @var int
     */
    protected $_priority = 50;

    /**
     * Resolve the request parameters into a controller and attach the controller
     * to the event object.
     *
     * @param \Bit\Event\Event $event The event instance.
     * @return void
     */
    public function beforeDispatch(Event $event)
    {
        $request = $event->data['request'];
        $response = $event->data['response'];
        $event->data['controller'] = $this->_getController($request, $response);
    }

    /**
     * Gets controller to use, either plugin or application controller.
     *
     * @param \Bit\Network\Request $request Request object
     * @param \Bit\Network\Response $response Response for the controller.
     * @return \Bit\Controller\Controller|false Object if loaded, boolean false otherwise.
     */
    protected function _getController($request, $response)
    {
        $pluginPath = $controller = null;
        $namespace = 'Controller';
        if (!empty($request->params['plugin'])) {
            $pluginPath = $request->params['plugin'] . '.';
        }
        if (!empty($request->params['controller'])) {
            $controller = $request->params['controller'];
        }
        if (!empty($request->params['prefix'])) {
            if (strpos($request->params['prefix'], '/') === false) {
                $namespace .= '/' . Inflector::camelize($request->params['prefix']);
            } else {
                $prefixes = array_map(
                    'Bit\Utility\Inflector::camelize',
                    explode('/', $request->params['prefix'])
                );
                $namespace .= '/' . implode('/', $prefixes);
            }
        }
        $firstChar = substr($controller, 0, 1);
        if (strpos($controller, '\\') !== false ||
            strpos($controller, '.') !== false ||
            $firstChar === strtolower($firstChar)
        ) {
            return false;
        }
        $className = false;
        if ($pluginPath . $controller) {
            $className = Bit::classname($pluginPath . $controller, $namespace, 'Controller');
        }

        if (!$className) {
            return false;
        }
        
        $reflection = new ReflectionClass($className);
        if ($reflection->isAbstract() || $reflection->isInterface()) {
            return false;
        }
        return $reflection->newInstance($request, $response, $controller);
    }
}
