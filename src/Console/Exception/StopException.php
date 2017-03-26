<?php
namespace Bit\Console\Exception;

use Bit\Core\Exception\Exception;

/**
 * Exception class for halting errors in console tasks
 *
 * @see \Bit\Console\Shell::_stop()
 * @see \Bit\Console\Shell::error()
 */
class StopException extends Exception
{
}
