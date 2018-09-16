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

namespace Bit\Console;

use Bit\Console\Exception\MissingHelperException;
use Bit\Core\Bit;
use Bit\Core\ObjectRegistry;

/**
 * Registry for Helpers. Provides features
 * for lazily loading helpers.
 */
class HelperRegistry extends ObjectRegistry
{

    /**
     * Shell to use to set params to tasks.
     *
     * @var \Bit\Console\ConsoleIo
     */
    protected $_io;

    /**
     * Sets The IO instance that should be passed to the shell helpers
     *
     * @param \Bit\Console\ConsoleIo $io An io instance.
     * @return void
     */
    public function setIo(ConsoleIo $io)
    {
        $this->_io = $io;
    }

    /**
     * Resolve a helper classname.
     *
     * Part of the template method for Bit\Core\ObjectRegistry::load()
     *
     * @param string $class Partial classname to resolve.
     * @return string|false Either the correct classname or false.
     */
    protected function _resolveClassName($class)
    {
        return Bit::className($class, 'Shell/Helper', 'Helper');
    }

    /**
     * Throws an exception when a helper is missing.
     *
     * Part of the template method for Bit\Core\ObjectRegistry::load()
     *
     * @param string $class The classname that is missing.
     * @param string $plugin The plugin the helper is missing in.
     * @return void
     * @throws \Bit\Console\Exception\MissingHelperException
     */
    protected function _throwMissingClassError($class, $plugin)
    {
        throw new MissingHelperException([
            'class' => $class,
            'plugin' => $plugin
        ]);
    }

    /**
     * Create the helper instance.
     *
     * Part of the template method for Bit\Core\ObjectRegistry::load()
     *
     * @param string $class The classname to create.
     * @param string $alias The alias of the helper.
     * @param array $settings An array of settings to use for the helper.
     * @return \Bit\Console\Helper The constructed helper class.
     */
    protected function _create($class, $alias, $settings)
    {
        return new $class($this->_io, $settings);
    }
}
