<?php
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
     */
    public function init(array $config = [])
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function gc($expires = null)
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function write($key, $value)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function writeMany($data)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function read($key)
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function readMany($keys)
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function increment($key, $offset = 1)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function decrement($key, $offset = 1)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function delete($key)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function deleteMany($keys)
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function clear($check)
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function clearGroup($group)
    {
        return false;
    }
}
