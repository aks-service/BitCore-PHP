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

use Bit\Chronos\ChronosInterface;

/**
 * Provides methods for setting a 'test' now. This lets you
 * retrieve pre-determined times with now().
 */
trait TestingAidTrait
{
    /**
     * A test ChronosInterface instance to be returned when now instances are created
     *
     * @var ChronosInterface
     */
    protected static $testNow;

    /**
     * Set a ChronosInterface instance (real or mock) to be returned when a "now"
     * instance is created.  The provided instance will be returned
     * specifically under the following conditions:
     *   - A call to the static now() method, ex. ChronosInterface::now()
     *   - When a null (or blank string) is passed to the constructor or parse(), ex. new Chronos(null)
     *   - When the string "now" is passed to the constructor or parse(), ex. new Chronos('now')
     *
     * Note the timezone parameter was left out of the examples above and
     * has no affect as the mock value will be returned regardless of its value.
     *
     * To clear the test instance call this method using the default
     * parameter of null.
     *
     * @param ChronosInterface|null $testNow The instance to use for all future instances.
     * @return void
     */
    public static function setTestNow(ChronosInterface $testNow = null)
    {
        static::$testNow = $testNow;
    }

    /**
     * Get the ChronosInterface instance (real or mock) to be returned when a "now"
     * instance is created.
     *
     * @return static the current instance used for testing
     */
    public static function getTestNow()
    {
        return static::$testNow;
    }

    /**
     * Determine if there is a valid test instance set. A valid test instance
     * is anything that is not null.
     *
     * @return bool True if there is a test instance, otherwise false
     */
    public static function hasTestNow()
    {
        return static::getTestNow() !== null;
    }
}
