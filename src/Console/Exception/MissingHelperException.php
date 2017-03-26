<?php
namespace Bit\Console\Exception;

use Bit\Core\Exception\Exception;

/**
 * Used when a Helper cannot be found.
 *
 */
class MissingHelperException extends Exception
{

    protected $_messageTemplate = 'Helper class %s could not be found.';
}
