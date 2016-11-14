<?php
namespace Bit\PHPQuery\Exception;

use Bit\Core\Exception\Exception;

/**
 * Exception raised when a plugin could not be found
 *
 */
class MissingPhpQueryPluginException extends Exception
{

    protected $_messageTemplate = 'PHPQuery Plugin %s could not be found.';
}
