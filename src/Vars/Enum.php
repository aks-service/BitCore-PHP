<?php
/**
 * BitCore-PHP:  Rapid Development Framework (https://phpcore.bitcoding.eu)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          https://phpcore.bitcoding.eu BitCore-PHP Project
 * @since         0.1.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Bit\Vars;
/**
 * Class Enum
 * Working With Const as C Enum
 * @package Bit\Vars
 */
abstract class Enum {
    /**
     * Value of enum
     * @var int|mixed|null
     */
    protected $_value = null;

    /**
     * Convert input in Enum.
     * @param $var
     */
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

    /**
     * return raw value
     * @return int|mixed|null
     */
    public function get(){
        return $this->_value;
    }

    /**
     * get Name
     * @return int|null|string
     */
    public function getName(){
        return static::getNamebyValue($this->_value);
    }

    /**
     * Cache Class Constants
     * @var null|mixed
     */
    private static $constCacheArray = NULL;

    /**
     * Cache Enum Class
     * @return mixed
     */
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

    /**
     * check Is contant valid
     * @param $name
     * @param bool $strict
     * @return bool
     */
    public static function isValidName($name, $strict = false) {
        $constants = self::getConstants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));
        return in_array(strtolower($name), $keys);
    }

    /**
     * Check if value valid
     * @param $value
     * @return bool
     */
    public static function isValidValue($value) {
        $values = array_values(self::getConstants());
        return in_array($value, $values, $strict = true);
    }

    /**
     * get Contants
     * @return mixed
     */
    public static function getConstList() {
        return self::getConstants();
    }

    /**
     * Get Name By Value
     * @param $value
     * @return int|null|string
     */
    public static function getNamebyValue($value) {
        $constants = self::getConstList();
        foreach($constants as $key =>$val){
            if($val === $value)
                return $key;
        }
        return null;
    }

    /**
     * Return Constant value by Name
     * @param $name
     * @return mixed
     */
    public static function getValuebyName($name) {
        $constants = self::getConstList();

        return $constants[$name];
    }

    /**
     * convert Constant to
     * @return int|null|string
     */
    public function __toString()
    {
        return static::getNamebyValue($this->_value);
    }
}
