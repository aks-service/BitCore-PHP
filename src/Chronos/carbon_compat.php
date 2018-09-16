<?php
/**
 * BitCore-PHP:  Rapid Development Framework (https://phpcore.bitcoding.eu)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          https://phpcore.bitcoding.eu BitCore-PHP Project
 * @since         0.7.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

// Create class aliases for Carbon so applications
// can upgrade more easily.
class_alias('Bit\Chronos\Chronos', 'Carbon\MutableDateTime');
class_alias('Bit\Chronos\ChronosInterface', 'Carbon\CarbonInterface');
