<?php
/**
 * BitCore-PHP:  Rapid Development Framework (https://phpcore.bitcoding.eu)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          https://phpcore.bitcoding.eu BitCore-PHP Project
 * @since         0.8.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Bit\Parser;

use Bit\Core\ObjectRegistry;
use Bit\Core\Traits\StaticConfig;
use Bit\Parser\Parser\Document;

//define('DELIM',UTF8::HtmlChar(0xFAAA));


/**
 * Class Parser
 * @package Parser
 */
class Parser 
{

    use StaticConfig;

    /**
     * An array mapping url schemes to fully qualified parser engine
     * class names.
     *
     * @var array
     */
    protected static $_dsnClassMap = [
        'bbcode' => 'Parser\Engine\BBCodeEngine'//,
        //'wiki' => 'Parser\Engine\WikiEngine' //TODO
    ];

    /**
     * Cache Registry used for creating and using cache adapters.
     *
     * @var \Bit\Parser\ParserRegistry
     */
    protected static $_registry;

    /**
     * Returns the Parser Registry instance used for creating and using parser adapters.
     * Also allows for injecting of a new registry instance.
     *
     * @param \Bit\Parser\ParserRegistry|null $registry Injectable registry object.
     * @return \Bit\Parser\ParserRegistry
     */
    public static function registry(ObjectRegistry $registry = null)
    {
        if ($registry) {
            static::$_registry = $registry;
        }

        if (empty(static::$_registry)) {
            static::$_registry = new ParserRegistry();
        }

        return static::$_registry;
    }

    /**
     * Finds and builds the instance of the required engine class.
     *
     * @param string $name Name of the config array that needs an engine instance built
     * @return void
     * @throws \InvalidArgumentException When a cache engine cannot be created.
     */
    protected static function _buildEngine($name)
    {
        $registry = static::registry();

        if (empty(static::$_config[$name]['className'])) {
            //TODO
            throw new InvalidArgumentException(
                sprintf('The "%s" Parser configuration does not exist.', $name)
            );
        }

        $config = static::$_config[$name];
        $registry->load($name, $config);
    }

    /**
     * Fetch the engine attached to a specific configuration name.
     *
     * If the cache engine & configuration are missing an error will be
     * triggered.
     *
     * @param string $config The configuration name you want an engine for.
     * @return \Bit\Parser\ParserEngine
     */
    public static function engine($config)
    {
        $registry = static::registry();

        if (isset($registry->{$config})) {
            return $registry->{$config};
        }

        static::_buildEngine($config);
        return $registry->{$config};
    }

    /**
     * parse text to Document
     * @param string $text
     * @param string $config
     * @return Document
     */
    public static function parse(string $text, $config = 'default')
    {
        $engine = static::engine($config);
        return $engine->parse($text);
    }

    /**
     * Render Document
     * @param Document $document
     * @param string $config
     * @return string
     */
    public static function render(Document $document, $config = 'default')    {
        $engine = static::engine($config);
        return $engine->render($document);
    }

    /**
     * combine parse and renderer
     * @param string $text
     * @param string $config
     * @return string
     */
    public static function simple(string $text, $config = 'default'){
        $engine = static::engine($config);
        return $engine->render($engine->parse($text));
    }

}
