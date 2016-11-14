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

class Matrix extends Vars{
    const IS = 'is_array';
    const GET = FILTER_SANITIZE_STRING;
    
    function get(){
        return (array) $this->_var;
    }
    
    public static function MergeRecursiveDistinct(array $array1, array &$array2, $depth = 0) {
        $merged = $array1;

        foreach ($array1 as $key => $value) {
            if (isset($array2[$key])) {
                if ($depth && is_array($array2[$key])) {
                    $merged [$key] = self::MergeRecursiveDistinct($array2[$key], $value, $depth--);
                } else {
                    $merged [$key] = $array2[$key];
                }
            }
        }

        return $merged;
    }
}