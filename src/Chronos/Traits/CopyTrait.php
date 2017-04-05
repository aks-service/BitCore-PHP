<?php
namespace Bit\Chronos\Traits;

/**
 * Provides methods for copying datetime objects.
 *
 * Expects that implementing classes provide a static `instance()` method.
 */
trait CopyTrait
{
    /**
     * Get a copy of the instance
     *
     * @return static
     */
    public function copy()
    {
        return static::instance($this);
    }
}
