<?php
namespace Bit\Controller\Exception;

use Bit\Core\Exception\Exception;

/**
 * Missing Action exception - used when a controller action
 * cannot be found, or when the controller's isAction() method returns false.
 */
class MissingActionException extends Exception
{

    /**
     * {@inheritDoc}
     */
    protected $_messageTemplate = 'Action %s::%s() could not be found, or is not accessible.';

    /**
     * {@inheritDoc}
     */
    public function __construct($message, $code = 404)
    {
        parent::__construct($message, $code);
    }
}
