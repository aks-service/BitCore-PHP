<?php
namespace Bit\Controller\Exception;

use Bit\Core\Exception\Exception;

/**
 * Used when a view file for a cell cannot be found.
 */
class MissingCellViewException extends Exception
{

    protected $_messageTemplate = 'Cell view file "%s" is missing.';
}
