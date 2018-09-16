<?php
/**
 * BitCore-PHP:  Rapid Development Framework (https://phpcore.bitcoding.eu)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          https://phpcore.bitcoding.eu BitCore-PHP Project
 * @since         0.1.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Bit\Core\Exception;

/**
 * Class Printnice
 * TODO
 * @since       0.1.0
 */
class PrintNiceException extends Exception {
    /**
     * PrintNiceException constructor.
     * @param $message
     * @param null $code
     * @param int $error
     * @param null $previous
     */
    public function __construct($message, $code = null, $error = 500, $previous = null) {

        parent::__construct('<pre style="font-size: 12px; line-height: ' . $this->_fontsize . 'px;">' . Bit::_debug($this->_var) . '</pre>', $error, $previous);
    }
}
