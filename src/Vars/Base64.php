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
use Bit\Database\Driver;

class Base64 extends Vars{
    const SET = FILTER_VALIDATE_REGEXP;
    const IS = FILTER_VALIDATE_REGEXP;
    const GET = FILTER_SANITIZE_STRING;
    
    protected $_defaultConfig = [
        'set'=> [
            'options'=> [
                'regexp' => '/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i'
                ]
            ],
        'is'=> [
            'options'=> [
                'regexp' => '/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i'
                ]
            ]
        ];
}
