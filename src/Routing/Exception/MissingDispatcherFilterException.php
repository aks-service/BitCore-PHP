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
 * Exception raised when a Dispatcher filter could not be found
 *
 */
class MissingDispatcherFilterException extends Exception
{
    /**
     * {@inheritDoc}
     */
    protected $_messageTemplate = 'Dispatcher filter %s could not be found.';
}
