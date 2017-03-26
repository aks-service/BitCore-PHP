<?php
namespace Bit\Console\Exception;

use Bit\Core\Exception\Exception;

/**
 * Used when a Task cannot be found.
 *
 */
class MissingTaskException extends Exception
{

    protected $_messageTemplate = 'Task class %s could not be found.';
}
