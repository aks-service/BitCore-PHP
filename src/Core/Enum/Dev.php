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
namespace Bit\Core\Enum;
use Bit\Vars\Enum;

class Dev extends Enum{
    const RELEASE = 0x0000;
    const SIMPLE = 0x1000;
    const DEBUG = 0x2000;
}