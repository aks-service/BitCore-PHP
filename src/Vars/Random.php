<?php
/* 
 * BitCore (tm) : Bit Development Framework
 * Copyright (c) BitCore
 * 
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 * 
 * @copyright     BitCore
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Bit\Vars;
use Bit\Core\Vars;

class Random extends Vars{
    const IS = FILTER_VALIDATE_INT;
    const GET = FILTER_SANITIZE_NUMBER_INT;
    const CAST = 'intval';
        
    CONST UPPER = 0x0001;
    CONST LOWER = 0x0002;
    CONST NUMERIC = 0x0004;
    CONST SPECIAL = 0x0008;
    CONST ALL = 0x000F;
    CONST TMP_UPPER = 'WERTZUPLKJHGFDSAYXCVBNM';
    CONST TMP_LOWER = 'qwertzupasdfghkyxcvbnm';
    CONST TMP_NUMERIC = '1234567890';
    
    

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