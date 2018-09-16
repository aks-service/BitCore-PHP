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
 * ENUM Mode
 * @package Bit\Core\Enum
 */
class Mode extends Enum{
    /**
     * PHP-Runtime
     */
    const RUNTIME = 0x0000;

    /**
     * Cli-Mode
     */
    const CLI = 0x0010;

    /**
     * Page-Mode
     */
    const PAGE = 0x0020;

    /**
     * Custom Mode 1
     */
    const CUSTOM1 = 0x0040;

    /**
     * Custom Mode 2
     */
    const CUSTOM2 = 0x0080;
}