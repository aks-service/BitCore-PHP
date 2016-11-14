<?php
/** 
 * BitCore (tm) : Bit Development Framework
 * Copyright (c) BitCore
 * 
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 * 
 * @copyright     BitCore
 * @since         1.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Bit\Routing\Router;

define('TIME_START', microtime(true));

require BIT . 'basics.php';

// Sets the initial router state so future reloads work.
Router::reload();
