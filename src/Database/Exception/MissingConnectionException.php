<?php
namespace Bit\Database\Exception;

use Bit\Core\Exception\Exception;

class MissingConnectionException extends Exception
{

    /**
     * {@inheritDoc}
     */
    protected $_messageTemplate = 'Connection to database could not be established: %s';
}
