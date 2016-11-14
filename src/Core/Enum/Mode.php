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

class Mode extends Enum{
/**
     * PHP-Runtime
     */
    const RUNTIME = 0x0000;

    /**
     * Cli-Mode
     */
    const CLI = 0x0010;

    /**
     * Page-Mode
     */
    const PAGE = 0x0020;

    /**
     * Custom Mode 1
     */
    const CUSTOM1 = 0x0040;

    /**
     * Custom Mode 2
     */
    const CUSTOM2 = 0x0080;
}