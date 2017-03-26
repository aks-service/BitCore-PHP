<?php
namespace Bit\Console\Exception;

use Bit\Core\Exception\Exception;

/**
 * Used when a shell cannot be found.
 *
 */
class MissingShellException extends Exception
{

    protected $_messageTemplate = 'Shell class for "%s" could not be found.';
}
