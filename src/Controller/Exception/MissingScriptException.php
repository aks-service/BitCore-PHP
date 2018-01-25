<?php
namespace Bit\Controller\Exception;

use Bit\Core\Exception\Exception;

/**
 * Used when a component cannot be found.
 *
 */
class MissingScriptException extends Exception
{

    protected $_messageTemplate = 'Script %s could not be found.';
}
