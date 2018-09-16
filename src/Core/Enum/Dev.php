<?php
/**
 * BitCore-PHP:  Rapid Development Framework (https://phpcore.bitcoding.eu)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          https://phpcore.bitcoding.eu BitCore-PHP Project
 * @since         0.1.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Bit\Core\Enum;
use Bit\Vars\Enum;

/**
 * ENUM Dev
 * @package Bit\Core\Enum
 */
class Dev extends Enum{
    const RELEASE = 0x0000;
    const SIMPLE = 0x1000;
    const DEBUG = 0x2000;
}