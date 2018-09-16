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
 * Not Implemented Exception - used when an API method is not implemented
 *
 */
class NotImplementedException extends HttpException
{

    /**
     * {@inheritDoc}
     */
    protected $_messageTemplate = '%s is not implemented.';

    /**
     * {@inheritDoc}
     *
     * NotImplementedException constructor.
     * @param $message
     * @param int $code
     */
    public function __construct($message, $code = 501)
    {
        parent::__construct($message, $code);
    }
}
