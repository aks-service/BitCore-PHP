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

namespace Bit\LessPHP\Traits;
use Bit\LessPHP\Reflector as Reflect;

/**
 * Trait Reflector
 * @package Bit\LessPHP\Traits
 */
trait Reflector{
    use DocComment;

    /**
     * Reflect Infos Array
     * @var null
     */
    protected $_reflect = [];


    /**
     * Reflect a Class
     * @return \ReflectionClass[]|null
     */
    public function reflect(){
        if(!empty($this->_reflect))
            return null;
        
        $class  = get_class($this);

        if(!isset($this->_reflect[$class]))
            $this->_reflect[$class]  = new \ReflectionClass($class);

        while($pClass = get_parent_class($class)){
            $this->_reflect[$pClass] = new \ReflectionClass($pClass);
            $class = $pClass;
        }

        return empty($this->_reflect) ? null : $this->_reflect ;
    }
}