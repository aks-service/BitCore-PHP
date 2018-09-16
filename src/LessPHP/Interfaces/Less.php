<?php
/**
 * BitCore-PHP:  Rapid Development Framework (https://phpcore.bitcoding.eu)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          https://phpcore.bitcoding.eu BitCore-PHP Project
 * @since         0.4.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Bit\LessPHP\Interfaces;

/**
 * LessInterface is the interface that should be implemented
 * by all classes that are going to be used as Less worker.
 */
interface Less
{
    /**
     * \ReflectionClass[]|null
     */
    public function reflect();
}