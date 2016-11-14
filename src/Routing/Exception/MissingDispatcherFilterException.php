<?php
namespace Bit\Routing\Exception;

use Bit\Core\Exception\Exception;

/**
 * Exception raised when a Dispatcher filter could not be found
 *
 */
class MissingDispatcherFilterException extends Exception
{

    protected $_messageTemplate = 'Dispatcher filter %s could not be found.';
}
