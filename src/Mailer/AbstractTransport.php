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

namespace Bit\Mailer;

use Bit\Core\Traits\InstanceConfig;

/**
 * Abstract transport for sending email
 *
 */
abstract class AbstractTransport
{

    use InstanceConfig;

    /**
     * Default config for this class
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Send mail
     *
     * @param \Bit\Mailer\Email $email Email instance.
     * @return array
     */
    abstract public function send(Email $email);

    /**
     * Constructor
     *
     * @param array $config Configuration options.
     */
    public function __construct($config = [])
    {
        $this->config($config);
    }

    /**
     * Help to convert headers in string
     *
     * @param array $headers Headers in format key => value
     * @param string $eol End of line string.
     * @return string
     */
    protected function _headersToString($headers, $eol = "\r\n")
    {
        $out = '';
        foreach ($headers as $key => $value) {
            if ($value === false || $value === null || $value === '') {
                continue;
            }
            $out .= $key . ': ' . $value . $eol;
        }
        if (!empty($out)) {
            $out = substr($out, 0, -1 * strlen($eol));
        }
        return $out;
    }
}
