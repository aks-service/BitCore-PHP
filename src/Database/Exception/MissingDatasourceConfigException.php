<?php
namespace Bit\Database\Exception;

use Bit\Core\Exception\Exception;

/**
 * Exception class to be thrown when a datasource configuration is not found
 */
class MissingDatasourceConfigException extends Exception
{

    protected $_messageTemplate = 'The datasource configuration "%s" was not found.';
}
