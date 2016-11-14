<?php
namespace Bit\PHPQuery\Exception;

use Bit\Core\Exception\Exception;

/**
 * Used when a cell class file cannot be found.
 *
 */
class ExistMethodException extends Exception
{
    protected $_messageTemplate = 'Method %s exist.Use overwrite or other methodname.';
}
