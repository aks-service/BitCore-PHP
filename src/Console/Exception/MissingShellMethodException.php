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

namespace Bit\Console\Exception;

use Bit\Core\Exception\Exception;

/**
 * Used when a shell method cannot be found.
 *
 */
class MissingShellMethodException extends Exception
{
    /**
     * {@inheritDoc}
     */
    protected $_messageTemplate = "Unknown command %1\$s %2\$s.\nFor usage try `bit %1\$s --help`";
}
