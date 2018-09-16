<?php
/**
 * BitCore-PHP:  Rapid Development Framework (https://phpcore.bitcoding.eu)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          https://phpcore.bitcoding.eu BitCore-PHP Project
 * @since         0.5.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Bit\Routing\Route;

use Bit\Utility\Inflector;

/**
 * This route class will transparently inflect the controller and plugin routing
 * parameters, so that requesting `/my_controller` is parsed as `['controller' => 'MyController']`
 */
class InflectedRoute extends Route
{

    /**
     * Flag for tracking whether or not the defaults have been inflected.
     *
     * Default values need to be inflected so that they match the inflections that match()
     * will create.
     *
     * @var bool
     */
    protected $_inflectedDefaults = false;

    /**
     * Parses a string URL into an array. If it matches, it will convert the prefix, controller and
     * plugin keys to their camelized form.
     *
     * @param string $url The URL to parse
     * @return array|false An array of request parameters, or false on failure.
     */
    public function parse($url)
    {
        $params = parent::parse($url);
        if (!$params) {
            return false;
        }
        if (!empty($params['controller'])) {
            $params['controller'] = Inflector::camelize($params['controller']);
        }
        if (!empty($params['plugin'])) {
            if (strpos($params['plugin'], '/') === false) {
                $params['plugin'] = Inflector::camelize($params['plugin']);
            } else {
                list($vendor, $plugin) = explode('/', $params['plugin'], 2);
                $params['plugin'] = Inflector::camelize($vendor) . '/' . Inflector::camelize($plugin);
            }
        }
        return $params;
    }

    /**
     * Underscores the prefix, controller and plugin params before passing them on to the
     * parent class
     *
     * @param array $url Array of parameters to convert to a string.
     * @param array $context An array of the current request context.
     *   Contains information such as the current host, scheme, port, and base
     *   directory.
     * @return string|false Either a string URL for the parameters if they match or false.
     */
    public function match(array $url, array $context = [])
    {
        $url = $this->_underscore($url);
        if (!$this->_inflectedDefaults) {
            $this->_inflectedDefaults = true;
            $this->defaults = $this->_underscore($this->defaults);
        }
        return parent::match($url, $context);
    }

    /**
     * Helper method for underscoring keys in a URL array.
     *
     * @param array $url An array of URL keys.
     * @return array
     */
    protected function _underscore($url)
    {
        if (!empty($url['controller'])) {
            $url['controller'] = Inflector::underscore($url['controller']);
        }
        if (!empty($url['plugin'])) {
            $url['plugin'] = Inflector::underscore($url['plugin']);
        }
        return $url;
    }
}
