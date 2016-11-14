<?php
namespace Bit\Routing\Exception;

use Bit\Core\Exception\Exception;

/**
 * Exception raised when a URL cannot be reverse routed
 * or when a URL cannot be parsed.
 */
class MissingRouteException extends Exception
{

    /**
     * {@inheritDoc}
     */
    protected $_messageTemplate = 'A route matching "%s" could not be found.';

    /**
     * {@inheritDoc}
     */
    public function __construct($message, $code = 404)
    {
        parent::__construct($message, $code);
    }
}
