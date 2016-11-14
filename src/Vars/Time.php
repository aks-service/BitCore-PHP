<?php
namespace Bit\Vars;

use Bit\Database\Driver;
/**
 * Time type converter.
 *
 * Use to convert time instances to strings & back.
 */
class Time extends DateTime
{

    /**
     * Time format for DateTime object
     *
     * @var string
     */
    protected $_format = 'H:i:s';

    /**
     * {@inheritDoc}
     */
    protected function _parseValue($value)
    {
        $class = $this->_className;
        return $class::parseTime($value, $this->_localeFormat);
    }
}
