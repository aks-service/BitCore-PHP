<?php
namespace Bit\Core\Exception;

/**
 *
 * @author      Bitcoding <bitcoding@bitcoding.eu>
 * @copyright   Copyright &copy; 2009-2014, Bitcoding
 * @link        http://www.lessphp.eu/
 * @link        http://www.bitcoding.eu/
 * @license     http://www.bitcoding.eu/license/
 * 
 * @version     0.1.0 (Breadcrumb): PermissionException.php
 * @since       0.1.0
 * @package     System/Exception/PermissionException
 * @category    Exception
 */
class WrongVarException extends RbacException {
    protected $_messageTemplate = 'WrongVarType %s could not be save in this class.';
    
}

?>