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

use Bit\Database\Driver;
use Bit\Core\Vars;
use InvalidArgumentException;
use PDO;

class Text extends Vars{
    /**
     * Convert string data into the database format.
     *
     * @param mixed $this->_value The value to convert.
     * @param \Bit\Database\Driver $driver The driver instance to convert with.
     * @return string|null
     */
    public function toDatabase(Driver $driver = null)
    {
        if ($this->_value === null || is_string($this->_value)) {
            return $this->_value;
        }

        if (is_object($this->_value) && method_exists($this->_value, '__toString')) {
            return $this->_value->__toString();
        }

        if (is_scalar($this->_value)) {
            return (string)$this->_value;
        }

        throw new InvalidArgumentException('Cannot convert value to string');
    }

    /**
     * Convert string values to PHP strings.
     *
     * @param mixed $this->_value The value to convert.
     * @param \Bit\Database\Driver $driver The driver instance to convert with.
     * @return string|null
     */
    public function toPHP(Driver $driver = null)
    {
        if ($this->_value === null) {
            return null;
        }
        return (string)$this->_value;
    }

    /**
     * Get the correct PDO binding type for string data.
     *
     * @param mixed $this->_value The value being bound.
     * @param \Bit\Database\Driver $driver The driver.
     * @return int
     */
    public function toStatement(Driver $driver = null)
    {
        return PDO::PARAM_STR;
    }

    /**
     * Marshalls request data into PHP strings.
     *
     * @param mixed $this->_value The value to convert.
     * @return string|null Converted value.
     */
    public function marshal()
    {
        if ($this->_value === null) {
            return null;
        }
        if (is_array($this->_value)) {
            return '';
        }
        return (string)$this->_value;
    }

    /**
     * {@inheritDoc}
     *
     * @return boolean False as databse results are returned already as strings
     */
    public function requiresToPhpCast()
    {
        return false;
    }
}
