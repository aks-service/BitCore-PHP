<?php
/**
 * Project  Frostmourne HP (3.3.5/4.3.4 Full Support)
 *
 *  @link         http://www.frostmourne.eu/
 *  @copyright    Copyright (c) 2009 - 2016 Frostmourne
 *  @version      v4.0.1a
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

    public static function parse(string $text, $config = 'default')
    {
        $engine = static::engine($config);
        return $engine->parse($text);
    }

    public static function render(Document $document, $config = 'default')    {
        $engine = static::engine($config);
        return $engine->render($document);
    }

    public static function simple(string $text, $config = 'default'){
        $engine = static::engine($config);
        return $engine->render($engine->parse($text));
    }

}
