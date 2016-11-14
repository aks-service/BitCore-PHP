<?php
namespace Bit\Routing\Exception;

use Bit\Core\Exception\Exception;

/**
 * Missing Controller exception - used when a controller
 * cannot be found.
 *
 */
class MissingControllerException extends Exception
{

    /**
     * {@inheritDoc}
     */
    protected $_messageTemplate = 'Controller class %s could not be found.';

    /**
     * {@inheritDoc}
     */
    public function __construct($message, $code = 404)
    {
        parent::__construct($message,$this->_messageTemplate, $code);
    }
}
