<?php
namespace Bit\Network\Exception;

/**
 * Not Implemented Exception - used when an API method is not implemented
 *
 */
class NotImplementedException extends HttpException
{

    /**
     * {@inheritDoc}
     */
    protected $_messageTemplate = '%s is not implemented.';

    /**
     * {@inheritDoc}
     */
    public function __construct($message, $code = 501)
    {
        parent::__construct($message, $code);
    }
}
