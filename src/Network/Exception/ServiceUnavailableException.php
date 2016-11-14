<?php
namespace Bit\Network\Exception;

/**
 * Represents an HTTP 503 error.
 *
 */
class ServiceUnavailableException extends HttpException
{

    /**
     * Constructor
     *
     * @param string|null $message If no message is given 'Service Unavailable' will be the message
     * @param int $code Status code, defaults to 503
     */
    public function __construct($message = null, $code = 503)
    {
        if (empty($message)) {
            $message = 'Service Unavailable';
        }
        parent::__construct($message, $code);
    }
}
