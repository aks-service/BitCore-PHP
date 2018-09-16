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

use Bit\Core\Traits\InstanceConfig;
use Bit\Utility\Hash;
use Bit\Parser\Parser\Document;
use Bit\Parser\Parser\Node;

/**
 * Class ParserEngine
 * @package Bit\Parser
 */
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


    /**
     * Parse Text To ParseDocument
     * @param $text
     * @return Document
     */
    abstract function parse($text);

    /**
     * Document to Html
     * @param Node $node
     * @param null $list
     * @param bool $disable
     * @return string
     */
    abstract function render(Node $node,&$list = null ,$disable = false);

}