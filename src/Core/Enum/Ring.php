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

class Ring extends Enum{
    /**
     * PHP Run Level
     */
    const RUNLEVEL = 0x0000;

    /**
     * Kernel Mode
     */
    const KERNEL = 0x0001;

    /**
     * Prepare Mode
     */
    const PREPARE = 0x0002;

    /**
     * Render Mode
     */
    const RENDER = 0x0004;

    /**
     * Finish Mode
     */
    const FINISH = 0x0008; 
}