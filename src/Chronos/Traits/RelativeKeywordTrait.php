<?php
namespace Bit\Chronos\Traits;

/**
 * Provides methods for testing if strings contain relative keywords.
 */
trait RelativeKeywordTrait
{
    protected static $relativePattern = '/this|next|last|tomorrow|yesterday|today|[+-]|first|last|ago/i';

    /**
     * Determine if there is a relative keyword in the time string, this is to
     * create dates relative to now for test instances. e.g.: next tuesday
     *
     * @param string $time The time string to check.
     * @return bool true if there is a keyword, otherwise false
     */
    public static function hasRelativeKeywords($time)
    {
        // skip common format with a '-' in it
        if (preg_match('/[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}/', $time) !== 1) {
            return preg_match(static::$relativePattern, $time) > 0;
        }

        return false;
    }
}
