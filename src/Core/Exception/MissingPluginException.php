<?php
namespace Bit\Core\Exception;

/**
 * Exception raised when a plugin could not be found
 *
 */
class MissingPluginException extends Exception
{

    protected $_messageTemplate = 'Plugin %s could not be found.';
}
