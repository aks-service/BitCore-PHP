<?php
namespace Bit\Database\Exception;

use Bit\Core\Exception\Exception;

class MissingDriverException extends Exception
{

    /**
     * {@inheritDoc}
     */
    protected $_messageTemplate = 'Database driver %s could not be found.';
}
