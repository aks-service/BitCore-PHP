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

use PDO;

class Binary extends Vars{
    const IS = FILTER_VALIDATE_REGEXP;
    const GET = FILTER_SANITIZE_STRING;
    static $_options = ['is'=> ["options"=> ['regexp' => '/^[a-zA-Z0-9\/\r\n+]*={0,2}$/']]];
    
    
    /**
     * Convert binary data into the database format.
     *
     * Binary data is not altered before being inserted into the database.
     * As PDO will handle reading file handles.
     *
     * @param string|resource $value The value to convert.
     * @param \Bit\Database\Driver $driver The driver instance to convert with.
     * @return string|resource
     */
    public function toDatabase($value, Driver $driver)
    {
        return $value;
    }

    /**
     * Convert binary into resource handles
     *
     * @param null|string|resource $value The value to convert.
     * @param \Bit\Database\Driver $driver The driver instance to convert with.
     * @return resource|null
     * @throws \Bit\Core\Exception\Exception
     */
    public function toPHP($value, Driver $driver)
    {
        if ($value === null) {
            return null;
        }
        if (is_string($value) && $driver instanceof Sqlserver) {
            $value = pack('H*', $value);
        }
        if (is_string($value)) {
            return fopen('data:text/plain;base64,' . base64_encode($value), 'rb');
        }
        if (is_resource($value)) {
            return $value;
        }
        throw new Exception(sprintf('Unable to convert %s into binary.', gettype($value)));
    }

    /**
     * Get the correct PDO binding type for Binary data.
     *
     * @param mixed $value The value being bound.
     * @param \Bit\Database\Driver $driver The driver.
     * @return int
     */
    public function toStatement($value, Driver $driver)
    {
        return PDO::PARAM_LOB;
    }
}
