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

use RuntimeException;

/**
 * Exception class for Socket. This exception will be thrown from Socket, Email, HttpSocket
 * SmtpTransport, MailTransport and HttpResponse when it encounters an error.
 *
 */
class SocketException extends RuntimeException
{
}
