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

class Object extends Vars{
    const IS = 'is_object';
    const GET = FILTER_UNSAFE_RAW;
    
    function get(){
        return is_object($this->_var) ? $this->_var : false;
    }
}
