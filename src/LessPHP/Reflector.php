<?php
/**
 * BitCore-PHP:  Rapid Development Framework (https://phpcore.bitcoding.eu)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          https://phpcore.bitcoding.eu BitCore-PHP Project
 * @since         0.4.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Bit\LessPHP;

/**
 * Class Reflector
 * @package Bit\LessPHP
 *
 * @see \ReflectionClass
 */
class Reflector extends \ReflectionClass{
    /**
     * cache
     * @var array
     */
    private $_methods;

    /**
     * Reflector constructor.
     * @param $argument
     * @throws \ReflectionException
     */
    function __construct($argument) {
        parent::__construct($argument);
        
        foreach($this->getMethods() as $reflectmethod) {
            $this->_methods[$reflectmethod->getName()] = $reflectmethod;
        }
    }

    /**
     * methods
     * @param $name
     * @return null
     */
    function __get($name) {
        return isset($this->{'_'.$name}) ? $this->{'_'.$name} : null;
    }
}