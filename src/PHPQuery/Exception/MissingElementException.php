<?php
namespace Bit\PHPQuery\Exception;

use Bit\Core\Exception\Exception;

/**
 * Used when a cell class file cannot be found.
 *
 */
class MissingElementException extends Exception
{

    protected $_messageTemplate = 'Element is missing.';
}
