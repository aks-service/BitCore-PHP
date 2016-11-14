<?php
namespace Bit\Database\Exception;

use Bit\Core\Exception\Exception;

class MissingExtensionException extends Exception
{

    /**
     * {@inheritDoc}
     */
    protected $_messageTemplate = 'Database driver %s cannot be used due to a missing PHP extension or unmet dependency';
}
