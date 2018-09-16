<?php
/**
 * BitCore-PHP:  Rapid Development Framework (https://phpcore.bitcoding.eu)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          https://phpcore.bitcoding.eu BitCore-PHP Project
 * @since         0.5.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Bit\Routing\Exception;

use Bit\Core\Exception\Exception;

/**
 * Missing Controller exception - used when a controller
 * cannot be found.
 *
 */
class MissingControllerException extends Exception
{

    /**
     * {@inheritDoc}
     *
     * @var string
     */
    protected $_messageTemplate = 'Controller class %s could not be found.';

    /**
     * MissingControllerException constructor.
     *
     * {@inheritDoc}
     *
     * @param $message
     * @param int $code
     */
    public function __construct($message, $code = 404)
    {
        parent::__construct($message,$this->_messageTemplate, $code);
    }
}
