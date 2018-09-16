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

namespace Bit\Network\Http\Auth;

use Bit\Network\Http\Request;

/**
 * Basic authentication adapter for Bit\Network\Http\Client
 *
 * Generally not directly constructed, but instead used by Bit\Network\Http\Client
 * when $options['auth']['type'] is 'basic'
 */
class Basic
{

    /**
     * Add Authorization header to the request.
     *
     * @param \Bit\Network\Http\Request $request Request instance.
     * @param array $credentials Credentials.
     * @return void
     * @see http://www.ietf.org/rfc/rfc2617.txt
     */
    public function authentication(Request $request, array $credentials)
    {
        if (isset($credentials['username'], $credentials['password'])) {
            $value = $this->_generateHeader($credentials['username'], $credentials['password']);
            $request->header('Authorization', $value);
        }
    }

    /**
     * Proxy Authentication
     *
     * @param \Bit\Network\Http\Request $request Request instance.
     * @param array $credentials Credentials.
     * @return void
     * @see http://www.ietf.org/rfc/rfc2617.txt
     */
    public function proxyAuthentication(Request $request, array $credentials)
    {
        if (isset($credentials['username'], $credentials['password'])) {
            $value = $this->_generateHeader($credentials['username'], $credentials['password']);
            $request->header('Proxy-Authorization', $value);
        }
    }

    /**
     * Generate basic [proxy] authentication header
     *
     * @param string $user Username.
     * @param string $pass Password.
     * @return string
     */
    protected function _generateHeader($user, $pass)
    {
        return 'Basic ' . base64_encode($user . ':' . $pass);
    }
}
