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

/**
 * Represents an HTTP 409 error.
 *
 */
class ConflictException extends HttpException
{

    /**
     * Constructor
     *
     * @param string|null $message If no message is given 'Conflict' will be the message
     * @param int $code Status code, defaults to 409
     */
    public function __construct($message = null, $code = 409)
    {
        if (empty($message)) {
            $message = 'Conflict';
        }
        parent::__construct($message, $code);
    }
}
