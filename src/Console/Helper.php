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

use Bit\Core\Traits\InstanceConfig;

/**
 * Base class for Helpers.
 *
 * Console Helpers allow you to package up reusable blocks
 * of Console output logic. For example creating tables,
 * progress bars or ascii art.
 */
abstract class Helper
{
    use InstanceConfig;

    /**
     * Default config for this helper.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * ConsoleIo instance.
     *
     * @var \Bit\Console\ConsoleIo
     */
    protected $_io;

    /**
     * Constructor.
     *
     * @param \Bit\Console\ConsoleIo $io The ConsoleIo instance to use.
     * @param array $config The settings for this helper.
     */
    public function __construct(ConsoleIo $io, array $config = [])
    {
        $this->_io = $io;
        $this->config($config);
    }

    /**
     * This method should output content using `$this->_io`.
     *
     * @param array $args The arguments for the helper.
     * @return void
     */
    abstract public function output($args);
}
