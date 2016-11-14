<?php
namespace Bit\Controller\Exception;

use Bit\Network\Exception\BadRequestException;

/**
 * Security exception - used when SecurityComponent detects any issue with the current request
 */
class SecurityException extends BadRequestException
{
    /**
     * Security Exception type
     * @var string
     */
    protected $_type = 'secure';

    /**
     * Reason for request blackhole
     *
     * @var string
     */
    protected $_reason = null;

    /**
     * Getter for type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * Set Message
     *
     * @param string $message Exception message
     * @return void
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Set Reason
     *
     * @param string $reason Reason details
     * @return void
     */
    public function setReason($reason = null)
    {
        $this->_reason = $reason;
    }

    /**
     * Get Reason
     *
     * @return string
     */
    public function getReason()
    {
        return $this->_reason;
    }
}
