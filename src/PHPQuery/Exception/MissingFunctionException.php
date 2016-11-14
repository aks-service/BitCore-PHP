<?php
namespace Bit\PHPQuery\Exception;

use Bit\Core\Exception\Exception;

/**
 * Used when a cell class file cannot be found.
 *
 */
class MissingFunctionException extends Exception
{

    protected $_messageTemplate = 'Function %s is missing.';
}
