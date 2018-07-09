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

abstract class Enum {
    protected $_value = null;

    function __construct($var)
    {
        $val = $var;

        if(is_string($var) && static::isValidName($var,true))
        {
            $val = static::getValuebyName($var);
        }
        else if(is_string($var))
        {
            $val = intval($var);
        }
        $this->_value = $val;
    }


    public function get(){
        return $this->_value;
    }

    public function getName(){
        return static::getNamebyValue($this->_value);
    }


    private static $constCacheArray = NULL;
    
    protected static function getConstants() {
        if (self::$constCacheArray == NULL) {
            self::$constCacheArray = [];
        }
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new \ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }
        return self::$constCacheArray[$calledClass];
    }

    public static function isValidName($name, $strict = false) {
        $constants = self::getConstants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));
        return in_array(strtolower($name), $keys);
    }

    public static function isValidValue($value) {
        $values = array_values(self::getConstants());
        return in_array($value, $values, $strict = true);
    }
    
    public static function getConstList() {
        return self::getConstants();
    }

    public static function getNamebyValue($value) {
        $constants = self::getConstList();
        foreach($constants as $key =>$val){
            if($val === $value)
                return $key;
        }
        return null;
    }

    public static function getValuebyName($name) {
        $constants = self::getConstList();

        return $constants[$name];
    }

    public function __toString()
    {
        return static::getNamebyValue($this->_value);
    }
}
