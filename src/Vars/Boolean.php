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

class Boolean extends Vars{
    const IS = FILTER_VALIDATE_BOOLEAN;
    const GET = FILTER_VALIDATE_BOOLEAN;
    
    /**
     * Convert bool data into the database format.
     *
     * @param \Bit\Database\Driver $driver The driver instance to convert with.
     * @return bool|null
     */
    public function toDatabase(Driver $driver)
    {
        if ($this->_value === true || $this->_value === false || $this->_value === null) {
            return $this->_value;
        }

        if (in_array($this->_value, [1, 0, '1', '0'], true)) {
            return (bool)$this->_value;
        }

        throw new InvalidArgumentException('Cannot convert value to bool');
    }

    /**
     * Convert bool values to PHP booleans
     *
     * @param mixed $this->_value The value to convert.
     * @param \Bit\Database\Driver $driver The driver instance to convert with.
     * @return bool|null
     */
    public function toPHP(Driver $driver)
    {
        if ($this->_value === null) {
            return null;
        }
        if (is_string($this->_value) && !is_numeric($this->_value)) {
            return strtolower($this->_value) === 'true' ? true : false;
        }
        return !empty($this->_value);
    }

    /**
     * Get the correct PDO binding type for bool data.
     *
     * @param mixed $this->_value The value being bound.
     * @param \Bit\Database\Driver $driver The driver.
     * @return int
     */
    public function toStatement(Driver $driver)
    {
        if ($this->_value === null) {
            return PDO::PARAM_NULL;
        }

        return PDO::PARAM_BOOL;
    }

    /**
     * Marshalls request data into PHP booleans.
     *
     * @param mixed $this->_value The value to convert.
     * @return bool|null Converted value.
     */
    public function marshal()
    {
        if ($this->_value === null) {
            return null;
        }
        if ($this->_value === 'true') {
            return true;
        }
        if ($this->_value === 'false') {
            return false;
        }
        return !empty($this->_value);
    }
}
