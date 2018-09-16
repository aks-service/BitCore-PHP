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

namespace Bit\Log\Interfaces;

/**
 * LogStreamInterface is the interface that should be implemented
 * by all classes that are going to be used as Log streams.
 */
interface Log {

/**
 * Write method to handle writes being made to the Logger
 *
 * @param string $level The severity level of the message being written.
 *    See Bit\Log\Log::$_levels for list of possible levels.
 * @param string $message Message content to log
 * @param string|array $scope The scope(s) a log message is being created in.
 *    See Bit\Log\Log::config() for more information on logging scopes.
 * @return void
 */
	public function write($level, $message, $scope = []);
}
