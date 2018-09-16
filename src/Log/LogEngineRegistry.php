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

namespace Bit\Log;

use Bit\Core\Bit;
use Bit\Core\ObjectRegistry;
use Psr\Log\LoggerInterface;
use RuntimeException;

/**
 * Registry of loaded log engines
 */
class LogEngineRegistry extends ObjectRegistry
{

    /**
     * Resolve a logger classname.
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

        return Bit::className($class, 'Log/Engine', 'Log');
    }

    /**
     * Throws an exception when a logger is missing.
     *
     * Part of the template method for Bit\Core\ObjectRegistry::load()
     *
     * @param string $class The classname that is missing.
     * @param string $plugin The plugin the logger is missing in.
     * @return void
     * @throws \RuntimeException
     */
    protected function _throwMissingClassError($class, $plugin)
    {
        throw new RuntimeException(sprintf('Could not load class %s', $class));
    }

    /**
     * Create the logger instance.
     *
     * Part of the template method for Bit\Core\ObjectRegistry::load()
     *
     * @param string|\Psr\Log\LoggerInterface $class The classname or object to make.
     * @param string $alias The alias of the object.
     * @param array $settings An array of settings to use for the logger.
     * @return \Psr\Log\LoggerInterface The constructed logger class.
     * @throws \RuntimeException when an object doesn't implement the correct interface.
     */
    protected function _create($class, $alias, $settings)
    {
        if (is_callable($class)) {
            $class = $class($alias);
        }

        if (is_object($class)) {
            $instance = $class;
        }

        if (!isset($instance)) {
            $instance = new $class($settings);
        }

        if ($instance instanceof LoggerInterface) {
            return $instance;
        }

        throw new RuntimeException(
            'Loggers must implement Psr\Log\LoggerInterface.'
        );
    }

    /**
     * Remove a single logger from the registry.
     *
     * @param string $name The logger name.
     * @return void
     */
    public function unload($name)
    {
        unset($this->_loaded[$name]);
    }
}
