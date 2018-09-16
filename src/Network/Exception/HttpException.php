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

namespace Bit\Network\Exception;

use Bit\Core\Exception\Exception;

/**
 * Parent class for all of the HTTP related exceptions in BitPHP.
 * All HTTP status/error related exceptions should extend this class so
 * catch blocks can be specifically typed.
 *
 * You may also use this as a meaningful bridge to Bit\Core\Exception\Exception, e.g.:
 * throw new \Bit\Network\Exception\HttpException('HTTP Version Not Supported', 505);
 *
 */
class HttpException extends Exception
{
}
