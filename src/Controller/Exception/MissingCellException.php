<?php
namespace Bit\Controller\Exception;

use Bit\Core\Exception\Exception;

/**
 * Used when a cell class file cannot be found.
 *
 */
class MissingCellException extends Exception
{

    protected $_messageTemplate = 'Cell class %s is missing.';
}
