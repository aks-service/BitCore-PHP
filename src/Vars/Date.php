<?php
namespace Bit\Vars;

use Bit\Database\Driver;
use Bit\Vars\DateTime as VarsDateTime;

class Date extends VarsDateTime
{

    /**
     * The class to use for representing date objects
     *
     * This property can only be used before an instance of this type
     * class is constructed. After that use `useMutable()` or `useImmutable()` instead.
     *
     * @var string
     * @deprecated Use DateType::useMutable() or DateType::useImmutable() instead.
     */
    public static $dateTimeClass = 'Bit\I18n\Date';

    /**
     * Date format for DateTime object
     *
     * @var string
     */
    protected $_format = 'Y-m-d';

    /**
     * Change the preferred class name to the FrozenDate implementation.
     *
     * @return $this
     */
    public function useImmutable()
    {
        $this->_setClassName('Bit\I18n\FrozenDate', 'DateTimeImmutable');
        return $this;
    }

    /**
     * Change the preferred class name to the mutable Date implementation.
     *
     * @return $this
     */
    public function useMutable()
    {
        $this->_setClassName('Bit\I18n\Date', 'DateTime');
        return $this;
    }

    /**
     * Convert request data into a datetime object.
     *
     * @param mixed $value Request data
     * @return \DateTime
     */
    public function marshal($value)
    {
        $date = parent::marshal($value);
        if ($date instanceof DateTime) {
            $date->setTime(0, 0, 0);
        }
        return $date;
    }

    /**
     * Convert strings into Date instances.
     *
     * @param string $value The value to convert.
     * @param \Bit\Database\Driver $driver The driver instance to convert with.
     * @return \Bit\I18n\Date|\DateTime
     */
    public function toPHP($value, Driver $driver)
    {
        $date = parent::toPHP($value, $driver);
        if ($date instanceof DateTime) {
            $date->setTime(0, 0, 0);
        }
        return $date;
    }

    /**
     * {@inheritDoc}
     */
    protected function _parseValue($value)
    {
        $class = $this->_className;
        return $class::parseDate($value, $this->_localeFormat);
    }
}
