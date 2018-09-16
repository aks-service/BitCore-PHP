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

namespace Bit\Chronos;

/**
 * Basic english only 'translator' for diffForHumans()
 */
class Translator
{
    /**
     * Translation strings.
     *
     * @var array
     */
    public static $strings = [
        'year' => '1 year',
        'year_plural' => '{count} years',
        'month' => '1 month',
        'month_plural' => '{count} months',
        'week' => '1 week',
        'week_plural' => '{count} weeks',
        'day' => '1 day',
        'day_plural' => '{count} days',
        'hour' => '1 hour',
        'hour_plural' => '{count} hours',
        'minute' => '1 minute',
        'minute_plural' => '{count} minutes',
        'second' => '1 second',
        'second_plural' => '{count} seconds',
        'ago' => '{time} ago',
        'from_now' => '{time} from now',
        'after' => '{time} after',
        'before' => '{time} before',
    ];

    /**
     * Check if a translation key exists.
     *
     * @param string $key The key to check.
     * @return bool Whether or not the key exists.
     */
    public function exists($key)
    {
        return isset(static::$strings[$key]);
    }

    /**
     * Get a plural message.
     *
     * @param string $key The key to use.
     * @param string $count The number of items in the translation.
     * @param array $vars Additional context variables.
     * @return string The translated message or ''.
     */
    public function plural($key, $count, array $vars = [])
    {
        if ($count == 1) {
            return $this->singular($key, $vars);
        }
        return $this->singular($key . '_plural', ['count' => $count] + $vars);
    }

    /**
     * Get a singular message.
     *
     * @param string $key The key to use.
     * @param array $vars Additional context variables.
     * @return string The translated message or ''.
     */
    public function singular($key, array $vars = [])
    {
        if (isset(static::$strings[$key])) {
            $varKeys = array_keys($vars);
            foreach ($varKeys as $i => $k) {
                $varKeys[$i] = '{' . $k . '}';
            }
            return str_replace($varKeys, $vars, static::$strings[$key]);
        }
        return '';
    }
}
