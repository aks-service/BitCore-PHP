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

namespace Bit\Parser\Engine;

use Bit\Parser\ParserEngine;
use Bit\Parser\Parser\Document;
use Bit\Parser\Parser\Node;

/**
 * Class WikiEngine
 * @package Bit\Parser\Engine
 */
class WikiEngine extends ParserEngine
{
    /**
     * {@inheritDoc}
     *
     * @param $text
     * @return Document|void
     */
    function parse($text){}

    /**
     * {@inheritDoc}
     *
     * @param Node $node
     * @param null $list
     * @param bool $disable
     * @return string|void
     */
    function render(Node $node,&$list = null ,$disable = false){}
}