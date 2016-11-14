<?php
/**
 * Project  Frostmourne HP (3.3.5/4.3.4 Full Support)
 *
 *  @link         http://www.frostmourne.eu/
 *  @copyright    Copyright (c) 2009 - 2016 Frostmourne
 *  @version      v4.0.1a
 */
namespace Bit\Parser;

use Bit\Core\Traits\InstanceConfig;
use Bit\Utility\Hash;
use Bit\Parser\Parser\Document;
use Bit\Parser\Parser\Node;

abstract class ParserEngine
{
    use InstanceConfig;

   // var $_parserMode = STRINGPARSER_MODE_SEARCH;
    
    
    /**
     * The default cache configuration is overridden in most adapters.
     * @var array
     */
    protected $_defaultConfig = [
        'parseMode'  => '',
        'renderMode' => '',
        'Rules' => [
            'enable'  => [],
            'disable' => [],
            'garbage' => []
        ]
    ];

    /**
     * Initialize the cache engine
     *
     * Called automatically by the cache frontend. Merge the runtime config with the defaults
     * before use.
     *
     * @param array $config Associative array of parameters for the engine
     * @return bool True if the engine has been successfully initialized, false if not
     */
    public function init(array $config = [])
    {
        $this->config($config);

        return true;
    }


    /*abstract*/
    abstract function parse($text);
    abstract function render(Node $node,&$list = null ,$disable = false);
    


    //static function engine();
}