<?php
namespace Bit\Network\Exception;

/**
 * Represents an HTTP 409 error.
 *
 */
class ConflictException extends HttpException
{

    /**
     * Constructor
     *
     * @param string|null $message If no message is given 'Conflict' will be the message
     * @param int $code Status code, defaults to 409
     */
    public function __construct($message = null, $code = 409)
    {
        if (empty($message)) {
            $message = 'Conflict';
        }
        parent::__construct($message, $code);
    }
}
