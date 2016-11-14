<?php
namespace Bit\Mailer\Exception;

use Bit\Core\Exception\Exception;

/**
 * Missing Action exception - used when a mailer action cannot be found.
 */
class MissingActionException extends Exception
{

    /**
     * {@inheritDoc}
     */
    protected $_messageTemplate = 'Mail %s::%s() could not be found, or is not accessible.';

    /**
     * {@inheritDoc}
     */
    public function __construct($message, $code = 404)
    {
        parent::__construct($message, $code);
    }
}
