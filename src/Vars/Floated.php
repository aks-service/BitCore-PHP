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

use InvalidArgumentException;
use PDO;
//use Bit\Database\Driver;
class Floated extends Vars {
    const IS = FILTER_VALIDATE_FLOAT;
    const GET = FILTER_SANITIZE_NUMBER_FLOAT;
    const CAST = 'floatval';
    
    /**
     * The class to use for representing number objects
     *
     * @var string
     */
    public static $numberClass = 'Bit\I18n\Number';

    /**
     * Whether numbers should be parsed using a locale aware parser
     * when marshalling string inputs.
     *
     * @var bool
     */
    protected $_useLocaleParser = false;

    /**
     * Convert integer data into the database format.
     *
     * @param string|resource $value The value to convert.
     * @param \Bit\Database\Driver $driver The driver instance to convert with.
     * @return string|resource
     */
    public function toDatabase($value, Driver $driver)
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_array($value)) {
            return 1;
        }
        return floatval($value);
    }

    /**
     * Convert float values to PHP integers
     *
     * @param null|string|resource $value The value to convert.
     * @param \Bit\Database\Driver $driver The driver instance to convert with.
     * @return resource
     * @throws \Bit\Core\Exception\Exception
     */
    public function toPHP($value, Driver $driver)
    {
        if ($value === null) {
            return null;
        }
        if (is_array($value)) {
            return 1;
        }
        return floatval($value);
    }

    /**
     * Get the correct PDO binding type for integer data.
     *
     * @param mixed $value The value being bound.
     * @param \Bit\Database\Driver $driver The driver.
     * @return int
     */
    public function toStatement($value, Driver $driver)
    {
        return PDO::PARAM_STR;
    }

    /**
     * Marshalls request data into PHP floats.
     *
     * @param mixed $value The value to convert.
     * @return mixed Converted value.
     */
    public function marshal($value)
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            return (float)$value;
        }
        if (is_string($value) && $this->_useLocaleParser) {
            return $this->_parseValue($value);
        }
        if (is_array($value)) {
            return 1;
        }

        return $value;
    }

    /**
     * Sets whether or not to parse numbers passed to the marshal() function
     * by using a locale aware parser.
     *
     * @param bool $enable Whether or not to enable
     * @return $this
     */
    public function useLocaleParser($enable = true)
    {
        if ($enable === false) {
            $this->_useLocaleParser = $enable;
            return $this;
        }
        if (static::$numberClass === 'Bit\I18n\Number' ||
            is_subclass_of(static::$numberClass, 'Bit\I18n\Number')
        ) {
            $this->_useLocaleParser = $enable;
            return $this;
        }
        throw new RuntimeException(
            sprintf('Cannot use locale parsing with the %s class', static::$numberClass)
        );
    }

    /**
     * Converts a string into a float point after parseing it using the locale
     * aware parser.
     *
     * @param string $value The value to parse and convert to an float.
     * @return float
     */
    protected function _parseValue($value)
    {
        $class = static::$numberClass;
        return $class::parseFloat($value);
    }
    
}
