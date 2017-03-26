<?php
namespace Bit\Console\Exception;

use Bit\Core\Exception\Exception;

/**
 * Used when a shell method cannot be found.
 *
 */
class MissingShellMethodException extends Exception
{

    protected $_messageTemplate = "Unknown command %1\$s %2\$s.\nFor usage try `bit %1\$s --help`";
}
