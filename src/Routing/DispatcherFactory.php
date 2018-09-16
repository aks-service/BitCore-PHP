<?php
/**
 * BitCore-PHP:  Rapid Development Framework (https://phpcore.bitcoding.eu)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          https://phpcore.bitcoding.eu BitCore-PHP Project
 * @since         0.5.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Bit\Routing;

use Bit\Core\Bit;
use Bit\Routing\Exception\MissingDispatcherFilterException;

/**
 * A factory for creating dispatchers with all the desired middleware
 * connected.
 */
class DispatcherFactory
{

    /**
     * Stack of middleware to apply to dispatchers.
     *
     * @var array
     */
    protected static $_stack = [];

    /**
     * Add a new middleware object to the stack of middleware
     * that will be executed.
     *
     * Instances of filters will be re-used across all sub-requests
     * in a request.
     *
     * @param string|\Bit\Routing\DispatcherFilter $filter Either the classname of the filter
     *   or an instance to use.
     * @param array $options Constructor arguments/options for the filter if you are using a string name.
     *   If you are passing an instance, this argument will be ignored.
     * @return \Bit\Routing\DispatcherFilter
     */
    public static function add($filter, array $options = [])
    {
        if (is_string($filter)) {
            $filter = static::_createFilter($filter, $options);
        }
        static::$_stack[] = $filter;
        return $filter;
    }

    /**
     * Create an instance of a filter.
     *
     * @param string $name The name of the filter to build.
     * @param array $options Constructor arguments/options for the filter.
     * @return \Bit\Routing\DispatcherFilter
     * @throws \Bit\Routing\Exception\MissingDispatcherFilterException When filters cannot be found.
     */
    protected static function _createFilter($name, $options)
    {
        $className = Bit::className($name, 'Routing/Filter', 'Filter');
        if (!$className) {
            $msg = sprintf('Cannot locate dispatcher filter named "%s".', $name);
            throw new MissingDispatcherFilterException($msg);
        }
        return new $className($options);
    }

    /**
     * Create a dispatcher that has all the configured middleware applied.
     *
     * @return \Bit\Routing\Dispatcher
     */
    public static function create()
    {
        $dispatcher = new Dispatcher();
        foreach (static::$_stack as $middleware) {
            $dispatcher->addFilter($middleware);
        }
        return $dispatcher;
    }

    /**
     * Get the connected dispatcher filters.
     *
     * @return array
     */
    public static function filters()
    {
        return static::$_stack;
    }

    /**
     * Clear the middleware stack.
     *
     * @return void
     */
    public static function clear()
    {
        static::$_stack = [];
    }
}
