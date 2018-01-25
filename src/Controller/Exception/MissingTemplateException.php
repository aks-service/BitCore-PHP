<?php
namespace Bit\Controller\Exception;

use Bit\Core\Exception\Exception;

/**
 * Used when a component cannot be found.
 *
 */
class MissingTemplateException extends Exception
{

    protected $_messageTemplate = 'Template %s could not be found.';
}
