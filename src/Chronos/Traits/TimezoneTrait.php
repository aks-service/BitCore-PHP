<?php
/**
 * BitCore-PHP:  Rapid Development Framework (https://phpcore.bitcoding.eu)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          https://phpcore.bitcoding.eu BitCore-PHP Project
 * @since         0.7.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Bit\Chronos\Traits;

/**
 * Methods for modifying/reading timezone data.
 */
trait TimezoneTrait
{
    /**
     * Alias for setTimezone()
     *
     * @param DateTimeZone|string $value The DateTimeZone object or timezone name to use.
     * @return static
     */
    public function timezone($value)
    {
        return $this->setTimezone($value);
    }

    /**
     * Alias for setTimezone()
     *
     * @param DateTimeZone|string $value The DateTimeZone object or timezone name to use.
     * @return static
     */
    public function tz($value)
    {
        return $this->setTimezone($value);
    }

    /**
     * Set the instance's timezone from a string or object
     *
     * @param DateTimeZone|string $value The DateTimeZone object or timezone name to use.
     * @return static
     */
    public function setTimezone($value)
    {
        return parent::setTimezone(static::safeCreateDateTimeZone($value));
    }
}
