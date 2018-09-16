<?php
/**
 * BitCore-PHP:  Rapid Development Framework (https://phpcore.bitcoding.eu)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          https://phpcore.bitcoding.eu BitCore-PHP Project
 * @since         0.7.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Bit\Core\Exception;
use RuntimeException;
use Bit\Traits\Translate;


/**
 * BException class
 *
 * BException is the base class for all BIT exceptions.
 *
 * BException provides the functionality of translating an error code
 * into a descriptive error message in a language that is preferred
 * by user browser. Additional parameters may be passed together with
 * the error code so that the translated message contains more detailed
 * information.
 *
 * By default, BException looks for a message file by calling
 * {@link getErrorMessageFile()} method, which uses Bit::CLASS_HTML_EXT
 *
 * file located under "self::LANG_DIR" folder
 * @author      Bitcoding <bitcoding@bitcoding.eu>
 * @copyright   Copyright &copy; 2009-2014, Bitcoding
 * @link        http://www.lessphp.eu/
 * @link        http://www.bitcoding.eu/
 * @license     http://www.bitcoding.eu/license/
 *
 * @version     0.1.0 (Breadcrumb): BitException.php
 * @since       0.1.0
 * @package     System/Exception/BException
 * @category    Exception
 */
class Exception extends RuntimeException {
    use Translate;
    
    
    /**
     * Array of attributes that are passed in from the constructor, and
     * made available in the view when a development error is displayed.
     *
     * @var array
     */
    protected $_attributes = [];
    /**
     * Template string that has attributes sprintf()'ed into it.
     *
     * @var string
     */
    protected $_messageTemplate = null;

    /**
     * Array of headers to be passed to Bit\Network\Response::header()
     *
     * @var array
     */
    protected $_responseHeaders = null;

    /**
     * Exception constructor.
     *
     * Allows you to create exceptions that are treated as framework errors and disabled
     * when debug = 0.
     *
     * @param string|array $message Either the string of the error message, or an array of attributes
     *   that are made available in the view, and sprintf()'d into Exception::$_messageTemplate
     * @param int $code The code of the error, is also the HTTP status code for the error.
     * @param int $error
     * @param \Exception|null $previous the previous exception.
     */
    public function __construct($message, $code = null, $error = 500, $previous = null)
    {
        if (is_array($message)) {
            $this->_attributes      = $message;
            $this->_messageTemplate = $this->_messageTemplate ? $this->_messageTemplate : $code;
            $message = $this->translateMessage($this->_messageTemplate,$this->_attributes);
        }
        parent::__construct($message, $error, $previous);
    }

    /**
     * {@inheritDoc}
     *
     * @return string
     */
    public function getFileMessage() {
        return __DIR__.'/Exception.lng';
    }

    /**
     * Get/set the response header to be used
     *
     * See also Bit\Network\Response::header()
     *
     * @param string|array|null $header An array of header strings or a single header string
     *  - an associative array of "header name" => "header value"
     *  - an array of string headers is also accepted
     * @param string|null $value The header value.
     * @return array
     */
    public function responseHeader($header = null, $value = null)
    {
        if ($header === null) {
            return $this->_responseHeaders;
        }
        if (is_array($header)) {
            return $this->_responseHeaders = $header;
        }
        $this->_responseHeaders = [$header => $value];
    }
}
