<?php
/**
 * BitCore-PHP:  Rapid Development Framework (https://phpcore.bitcoding.eu)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          https://phpcore.bitcoding.eu BitCore-PHP Project
 * @since         0.7.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Bit\Database;

use Bit\Core\Bit;
use Bit\Core\ObjectRegistry;
use Bit\Database\Exception\MissingDatasourceException;

/**
 * A registry object for connection instances.
 *
 * @see \Bit\Database\ConnectionManager
 */
class ConnectionRegistry extends ObjectRegistry
{

    /**
     * Resolve a datasource classname.
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
        return Bit::className($class, 'Datasource');
    }

    /**
     * Throws an exception when a datasource is missing
     *
     * Part of the template method for Bit\Core\ObjectRegistry::load()
     *
     * @param string $class The classname that is missing.
     * @param string $plugin The plugin the datasource is missing in.
     * @return void
     * @throws \Bit\Database\Exception\MissingDatasourceException
     */
    protected function _throwMissingClassError($class, $plugin)
    {
        throw new MissingDatasourceException([
            'class' => $class,
            'plugin' => $plugin,
        ]);
    }

    /**
     * Create the connection object with the correct settings.
     *
     * Part of the template method for Bit\Core\ObjectRegistry::load()
     *
     * If a callable is passed as first argument, The returned value of this
     * function will be the result of the callable.
     *
     * @param string|object|callable $class The classname or object to make.
     * @param string $alias The alias of the object.
     * @param array $settings An array of settings to use for the datasource.
     * @return object A connection with the correct settings.
     */
    protected function _create($class, $alias, $settings)
    {
        if (is_callable($class)) {
            return $class($alias);
        }

        if (is_object($class)) {
            return $class;
        }

        unset($settings['className']);
        return new $class($settings);
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
