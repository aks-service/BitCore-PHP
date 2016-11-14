<?php
namespace Bit\Network\Exception;

/**
 * Represents an HTTP 405 error.
 *
 */
class MethodNotAllowedException extends HttpException
{

    /**
     * Constructor
     *
     * @param string|null $message If no message is given 'Method Not Allowed' will be the message
     * @param int $code Status code, defaults to 405
     */
    public function __construct($message = null, $code = 405)
    {
        if (empty($message)) {
            $message = 'Method Not Allowed';
        }
        parent::__construct($message, $code);
    }
}
