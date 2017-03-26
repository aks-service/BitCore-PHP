<?php
namespace Bit\Shell;

use Bit\Console\Shell;
use Bit\Routing\Exception\MissingRouteException;
use Bit\Routing\Router;

/**
 * Provides interactive CLI tools for routing.
 *
 */
class RoutesShell extends Shell
{

    /**
     * Override main() to handle action
     * Displays all routes in an application.
     *
     * @return void
     */
    public function main()
    {
        $output = [
            ['Route name', 'URI template', 'Defaults']
        ];
        foreach (Router::routes() as $route) {
            $name = isset($route->options['_name']) ? $route->options['_name'] : $route->getName();
            $output[] = [$name, $route->template, json_encode($route->defaults)];
        }
        $this->helper('table')->output($output);
        $this->out();
    }

    /**
     * Checks a url for the route that will be applied.
     *
     * @param string $url The URL to parse
     * @return null|false
     */
    public function check($url)
    {
        try {
            $route = Router::parse($url);
            foreach (Router::routes() as $r) {
                if ($r->match($route)) {
                    $name = isset($r->options['_name']) ? $r->options['_name'] : $r->getName();
                    break;
                }
            }
            $output = [
                ['Route name', 'URI template', 'Defaults'],
                [$name, $url, json_encode($route)]
            ];
            $this->helper('table')->output($output);
            $this->out();
        } catch (MissingRouteException $e) {
            $this->err("<warning>'$url' did not match any routes.</warning>");
            $this->out();
            return false;
        }
    }

    /**
     * Generate a URL based on a set of parameters
     *
     * Takes variadic arguments of key/value pairs.
     * @return null|false
     */
    public function generate()
    {
        try {
            $args = $this->_splitArgs($this->args);
            $url = Router::url($args);
            $this->out("> $url");
            $this->out();
        } catch (MissingRouteException $e) {
            $this->err("<warning>The provided parameters do not match any routes.</warning>");
            $this->out();
            return false;
        }
    }

    /**
     * Get the option parser.
     *
     * @return \Bit\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->description(
            'Get the list of routes connected in this application. ' .
            'This tool also lets you test URL generation and URL parsing.'
        )->addSubcommand('check', [
            'help' => 'Check a URL string against the routes. ' .
                'Will output the routing parameters the route resolves to.'
        ])->addSubcommand('generate', [
            'help' => 'Check a routing array against the routes. ' .
                "Will output the URL if there is a match.\n\n" .
                "Routing parameters should be supplied in a key:value format. " .
                "For example `controller:Articles action:view 2`"
        ]);
        return $parser;
    }

    /**
     * Split the CLI arguments into a hash.
     *
     * @param array $args The arguments to split.
     * @return array
     */
    protected function _splitArgs($args)
    {
        $out = [];
        foreach ($args as $arg) {
            if (strpos($arg, ':') !== false) {
                list($key, $value) = explode(':', $arg);
                $out[$key] = $value;
            } else {
                $out[] = $arg;
            }
        }
        return $out;
    }
}
