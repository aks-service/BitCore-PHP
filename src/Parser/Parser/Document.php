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

namespace Bit\Parser\Parser;


/**
 * Class Document
 * @package \Bit\Parser\Parser
 * 
 * @param Node[] childNodes
 * @param array attributes
 * @param string name
 * @param string value
 */
class Document extends Node
{
    /**
     * {@inheritDoc}
     */
    protected $type = self::TYPE_DOCUMENT;

    /**
     * Simple Text Helper
     * @param $text
     * @return Text
     */
    function createNewText($text){
        return new Text($text);
    }

    /**
     * Simple Element Helper
     * @param $name
     * @return Element
     */
    function createNewElement($name){
        return new Element($name);
    }
}