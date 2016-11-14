<?php
namespace Bit\Log;

use Psr\Log\LogLevel;

/**
 * A trait providing an object short-cut method
 * to logging.
 */
trait LogTrait
{

    /**
     * Convenience method to write a message to Log. See Log::write()
     * for more information on writing to logs.
     *
     * @param mixed $msg Log message.
     * @param int|string $level Error level.
     * @param string|array $context Additional log data relevant to this message.
     * @return bool Success of log write.
     */
    public function log($msg, $level = LogLevel::ERROR, $context = [])
    {
        return Log::write($level, $msg, $context);
    }
}
