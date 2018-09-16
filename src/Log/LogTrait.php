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

use Psr\Log\LogLevel;

/**
 * A trait providing an object short-cut method
 * to logging.
 */
trait LogTrait
{

    /**
     * Convenience method to write a message to Log. See Log::write()
     * for more information on writing to logs.
     *
     * @param mixed $msg Log message.
     * @param int|string $level Error level.
     * @param string|array $context Additional log data relevant to this message.
     * @return bool Success of log write.
     */
    public function log($msg, $level = LogLevel::ERROR, $context = [])
    {
        return Log::write($level, $msg, $context);
    }
}
