<?php
namespace Bit\Mailer\Exception;

use Bit\Core\Exception\Exception;

/**
 * Used when a mailer cannot be found.
 *
 */
class MissingMailerException extends Exception
{

    protected $_messageTemplate = 'Mailer class "%s" could not be found.';
}
