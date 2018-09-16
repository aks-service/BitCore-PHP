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

namespace Bit\Database\Exception;

use Bit\Core\Exception\Exception;

/**
 * Exception class to be thrown when a datasource configuration is not found
 */
class MissingDatasourceConfigException extends Exception
{
    /**
     * {@inheritDoc}
     */
    protected $_messageTemplate = 'The datasource configuration "%s" was not found.';
}
