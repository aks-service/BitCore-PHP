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

namespace Bit\Cache\Engine;

use Bit\Cache\CacheEngine;

/**
 * Null cache engine, all operations return false.
 *
 * This is used internally for when Cache::disable() has been called.
 */
class NullEngine extends CacheEngine
{

    /**
     * {@inheritDoc}
     * @param array $config
     * @return bool
     */
    public function init(array $config = [])
    {
        return true;
    }

    /**
     * {@inheritDoc}
     * @param null $expires
     * @return bool|void
     */
    public function gc($expires = null)
    {
        return false;
    }

    /**
     * {@inheritDoc}
     * @param string $key
     * @param mixed $value
     * @return bool|void
     */
    public function write($key, $value)
    {
    }

    /**
     * {@inheritDoc}
     * @param array $data
     * @return array|void
     */
    public function writeMany($data)
    {
    }

    /**
     * {@inheritDoc}
     *
     * @param string $key
     * @return bool|mixed
     */
    public function read($key)
    {
        return false;
    }

    /**
     * {@inheritDoc}
     * @param array $keys
     * @return array
     */
    public function readMany($keys)
    {
        return [];
    }

    /**
     * {@inheritDoc}
     * @param string $key
     * @param int $offset
     * @return bool|int|void
     */
    public function increment($key, $offset = 1)
    {
    }

    /**
     * {@inheritDoc}
     * @param string $key
     * @param int $offset
     * @return bool|int|void
     */
    public function decrement($key, $offset = 1)
    {
    }

    /**
     * {@inheritDoc}
     * @param string $key
     * @return bool|void
     */
    public function delete($key)
    {
    }

    /**
     * {@inheritDoc}
     * @param array $keys
     * @return array
     */
    public function deleteMany($keys)
    {
        return [];
    }

    /**
     * {@inheritDoc}
     * @param bool $check
     * @return bool
     */
    public function clear($check)
    {
        return false;
    }

    /**
     * {@inheritDoc}
     * @param string $group
     * @return bool
     */
    public function clearGroup($group)
    {
        return false;
    }
}
