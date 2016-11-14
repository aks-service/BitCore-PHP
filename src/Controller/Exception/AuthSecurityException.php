<?php
namespace Bit\Controller\Exception;

/**
 * Auth Security exception - used when SecurityComponent detects any issue with the current request
 */
class AuthSecurityException extends SecurityException
{
    /**
     * Security Exception type
     * @var string
     */
    protected $_type = 'auth';
}
