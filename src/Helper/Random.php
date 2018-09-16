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

namespace Bit\Helper;
/**
 * Class Random
 * @package Bit\Helper
 * @since       0.1.0
 */
class Random {

    CONST UPPER = 0x0001;
    CONST LOWER = 0x0002;
    CONST NUMERIC = 0x0004;
    CONST SPECIAL = 0x0008;
    CONST ALL = 0x000F;
    CONST TMP_UPPER = 'WERTZUPLKJHGFDSAYXCVBNM';
    CONST TMP_LOWER = 'qwertzupasdfghkyxcvbnm';
    CONST TMP_NUMERIC = '1234567890';

    /**
     * Generate RandomString
     * @param int $l
     * @param int $mode
     * @return string
     */
    static function String($l = 12, $mode = self::ALL) {
        $key = '';
        $pool = '';
        if ($mode & self::LOWER)
            $pool .= self::TMP_LOWER;
        if ($mode & self::UPPER)
            $pool .= self::TMP_UPPER;
        if ($mode & self::NUMERIC)
            $pool .= self::TMP_NUMERIC;

        srand((double) microtime() * 1000000);
        for ($index = 0; $index < $l; $index++)
            $key .= substr($pool, (rand() % (strlen($pool))), 1);
        return $key;
    }

}
