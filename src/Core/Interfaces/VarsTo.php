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
namespace Bit\Interfaces;

interface VarsTo{
    
    
    public function toInt($var = null);
    public function toString($var = null);
    public function toBool($var = null);
    public function toFloat($var = null);
    
}