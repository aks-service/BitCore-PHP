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

use Bit\Console\Exception\MissingTaskException;
use Bit\Core\Bit;
use Bit\Core\ObjectRegistry;

/**
 * Registry for Tasks. Provides features
 * for lazily loading tasks.
 */
class TaskRegistry extends ObjectRegistry
{

    /**
     * Shell to use to set params to tasks.
     *
     * @var \Bit\Console\Shell
     */
    protected $_Shell;

    /**
     * Constructor
     *
     * @param \Bit\Console\Shell $Shell Shell instance
     */
    public function __construct(Shell $Shell)
    {
        $this->_Shell = $Shell;
    }

    /**
     * Resolve a task classname.
     *
     * Part of the template method for Bit\Core\ObjectRegistry::load()
     *
     * @param string $class Partial classname to resolve.
     * @return string|false Either the correct classname or false.
     */
    protected function _resolveClassName($class)
    {
        return Bit::className($class, 'Shell/Task', 'Task');
    }

    /**
     * Throws an exception when a task is missing.
     *
     * Part of the template method for Bit\Core\ObjectRegistry::load()
     *
     * @param string $class The classname that is missing.
     * @param string $plugin The plugin the task is missing in.
     * @return void
     * @throws \Bit\Console\Exception\MissingTaskException
     */
    protected function _throwMissingClassError($class, $plugin)
    {
        throw new MissingTaskException([
            'class' => $class,
            'plugin' => $plugin
        ]);
    }

    /**
     * Create the task instance.
     *
     * Part of the template method for Bit\Core\ObjectRegistry::load()
     *
     * @param string $class The classname to create.
     * @param string $alias The alias of the task.
     * @param array $settings An array of settings to use for the task.
     * @return \Bit\Console\Shell The constructed task class.
     */
    protected function _create($class, $alias, $settings)
    {
        return new $class($this->_Shell->io());
    }
}
