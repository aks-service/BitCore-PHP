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
//use Bit\Database\Driver;
class Integer extends Vars{
    const IS = FILTER_VALIDATE_INT;
    const GET = FILTER_SANITIZE_NUMBER_INT;
    const CAST = 'intval';
    
    function toIpv4($var = null){
        var_dump($var,long2ip($this->_var),$var ? $var : $this->_var);
        return (string) long2ip($var ? $var : $this->_var);
    }
    
    
     /**
     * Convert integer data into the database format.
     *
     * @param mixed $this->_value The value to convert.
     * @param \Bit\Database\Driver $driver The driver instance to convert with.
     * @return int
     */
    public function toDatabase(Driver $driver = null)
    {
        if ($this->_value === null || $this->_value === '') {
            return null;
        }

        if (!is_scalar($this->_value)) {
            throw new InvalidArgumentException('Cannot convert value to integer');
        }

        return (int)$this->_value;
    }

    /**
     * Convert integer values to PHP integers
     *
     * @param mixed $this->_value The value to convert.
     * @param \Bit\Database\Driver $driver The driver instance to convert with.
     * @return int
     */
    public function toPHP(Driver $driver = null)
    {
        if ($this->_value === null) {
            return null;
        }
        return (int)$this->_value;
    }

    /**
     * Get the correct PDO binding type for integer data.
     *
     * @param mixed $this->_value The value being bound.
     * @param \Bit\Database\Driver $driver The driver.
     * @return int
     */
    public function toStatement(Driver $driver = null)
    {
        return PDO::PARAM_INT;
    }

    /**
     * Marshalls request data into PHP floats.
     *
     * @param mixed $this->_value The value to convert.
     * @return int|null Converted value.
     */
    public function marshal()
    {
        if ($this->_value === null || $this->_value === '') {
            return null;
        }
        if (is_numeric($this->_value) || ctype_digit($this->_value)) {
            return (int)$this->_value;
        }
        if (is_array($this->_value)) {
            return 1;
        }
        return null;
    }
}
