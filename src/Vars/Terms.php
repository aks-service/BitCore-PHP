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

class Terms extends Vars{
    
    public function __construct($var) {
        $this->_var = is_string($var) ? new \DateTime($var) : new \DateTime("@$var");
    }
    
    function get() {
        return $this->_var;
    }
    
    function toInt(){
        return (int) $this->_var->getTimestamp();
    }
    
    static function getMicroTimeFloat() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }
    //
    public static function Diff($time,$init = -1) {
        $diff = date_diff(($init === -1  ? date_create() : date_create(date('Y-m-d H:i:s', $init))) , date_create(date('Y-m-d H:i:s', $time)) /*, $absolute*/);
        return $diff;
    }
}