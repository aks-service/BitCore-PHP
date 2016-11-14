<?php
namespace Bit\Network\Exception;

/**
 * Represents an HTTP 406 error.
 *
 */
class NotAcceptableException extends HttpException
{

    /**
     * Constructor
     *
     * @param string|null $message If no message is given 'Not Acceptable' will be the message
     * @param int $code Status code, defaults to 406
     */
    public function __construct($message = null, $code = 406)
    {
        if (empty($message)) {
            $message = 'Not Acceptable';
        }
        parent::__construct($message, $code);
    }
}
