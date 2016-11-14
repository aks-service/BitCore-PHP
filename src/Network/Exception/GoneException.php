<?php
namespace Bit\Network\Exception;

/**
 * Represents an HTTP 410 error.
 *
 */
class GoneException extends HttpException
{

    /**
     * Constructor
     *
     * @param string|null $message If no message is given 'Gone' will be the message
     * @param int $code Status code, defaults to 410
     */
    public function __construct($message = null, $code = 410)
    {
        if (empty($message)) {
            $message = 'Gone';
        }
        parent::__construct($message, $code);
    }
}
