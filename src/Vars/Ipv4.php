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

class Ipv4 extends Vars{
    const IS = FILTER_VALIDATE_IP;
    const GET = FILTER_SANITIZE_STRING;
    const CAST = 'String';
    
    
    public function toInt($var = null){
        
        return ip2long($var ? $var : $this->_var);
    }
    
    function toString($var = null){
        return (string) $var ? $var : $this->_var;
    }
}
