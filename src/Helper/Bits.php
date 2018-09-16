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

namespace Bit\Helper;

/**
 * Simple Helper class to working with $bits
 * @since       0.1.0
 */
class Bits {
    /**
     * Return true or false, depending on if the bit is set
     * @param $bf
     * @param $n
     * @return int
     */
    static function iSet(&$bf, $n) {$n = pow(2, $n);
        return ($bf & $n);
    }

    /**
     * Force a specific bit to ON
     * @param $bf
     * @param $n
     */
    static function setOn(&$bf, $n) {
        $bf |= pow(2, $n);
    }

    /**
     * Force a specific bit to be OFF
     * @param $bf
     * @param $n
     */
    static function setOff(&$bf, $n) {
        $bf &= ~(pow(2, $n));
    }

    /**
     * Toggle a bit, so bits that are on are turned off,
     * and bits that are off are turned on.
     * @param $bf
     * @param $n
     */
    static function Toogle(&$bf, $n) {
        $bf^= pow(2, $n);
    }

    /**
     * Set one Byte int8
     * @param $bf
     * @param $offset
     * @param $value
     */
    static function SetByteValue(&$bf, $offset, $value) {

        if ($bf>>($offset * 8) != $value) {
            $bf &= ~(0xFF<<($offset * 8));
            $bf |= $value<<($offset * 8);
        }
    }
}