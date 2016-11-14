<?php
namespace Bit\Network\Exception;

use Bit\Core\Exception\Exception;

/**
 * Parent class for all of the HTTP related exceptions in BitPHP.
 * All HTTP status/error related exceptions should extend this class so
 * catch blocks can be specifically typed.
 *
 * You may also use this as a meaningful bridge to Bit\Core\Exception\Exception, e.g.:
 * throw new \Bit\Network\Exception\HttpException('HTTP Version Not Supported', 505);
 *
 */
class HttpException extends Exception
{
}
