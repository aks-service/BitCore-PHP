<?php
namespace Bit\Database\Exception;

use Bit\Core\Exception\Exception;

/**
 * Used when a datasource cannot be found.
 *
 */
class MissingDatasourceException extends Exception
{

    protected $_messageTemplate = 'Datasource class %s could not be found. %s';
}
