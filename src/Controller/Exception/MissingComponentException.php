<?php
namespace Bit\Controller\Exception;

use Bit\Core\Exception\Exception;

/**
 * Used when a component cannot be found.
 *
 */
class MissingComponentException extends Exception
{

    protected $_messageTemplate = 'Component class %s could not be found.';
}
