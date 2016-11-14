<?php
namespace Bit\Routing\Route;

/**
 * Plugin short route, that copies the plugin param to the controller parameters
 * It is used for supporting /:plugin routes.
 *
 */
class PluginShortRoute extends InflectedRoute
{

    /**
     * Parses a string URL into an array. If a plugin key is found, it will be copied to the
     * controller parameter.
     *
     * @param string $url The URL to parse
     * @return array|false An array of request parameters, or boolean false on failure.
     */
    public function parse($url)
    {
        $params = parent::parse($url);
        if (!$params) {
            return false;
        }
        $params['controller'] = $params['plugin'];
        return $params;
    }

    /**
     * Reverses route plugin shortcut URLs. If the plugin and controller
     * are not the same the match is an auto fail.
     *
     * @param array $url Array of parameters to convert to a string.
     * @param array $context An array of the current request context.
     *   Contains information such as the current host, scheme, port, and base
     *   directory.
     * @return string|false Either a string URL for the parameters if they match or false.
     */
    public function match(array $url, array $context = [])
    {
        if (isset($url['controller'], $url['plugin']) && $url['plugin'] !== $url['controller']) {
            return false;
        }
        $this->defaults['controller'] = $url['controller'];
        $result = parent::match($url, $context);
        unset($this->defaults['controller']);
        return $result;
    }
}
