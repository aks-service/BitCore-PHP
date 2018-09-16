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
 * Class Node
 * @package \Bit\Parser\Parser
 * 
 * @param Node[] childNodes
 * @param array attributes
 * @param string name
 * @param string value
 */
class Node
{
    const TYPE_TEXT = 0;
    const TYPE_ELEMENT = 1;
    const TYPE_DOCUMENT = 2;

    /**
     * Node Name
     * @var string
     */
    protected $name = "";

    /**
     * Node Parent
     * @var Node|Document|Text|Element|null
     */
    protected $parent = null;

    /**
     * Node Type
     * @var integer
     */
    protected $type = self::TYPE_TEXT;
    
    /**
     * Node Value
     * @var string
     */
    protected $value = "";
    /**
     * Node is inline
     * @var bool
     */
    protected $inline = false;

    /**
     * Node constructor.
     * @param null $value
     */
    function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * Child Nodes
     * @var Node[]|null
     */
    protected $childNodes = [];

    /**
     * Node Attributes
     * @var []|null
     */
    protected $attributes = null;

    /**
     * Get protected
     *
     * @param $name
     * @return mixed
     */
    function __get($name)
    {
        return $this->{$name};
    }

    /**
     * TODO
     * @param $name
     * @param $value
     */
    function __set($name, $value)
    {
        $this->{$name} = $value;
    }

    /**
     * Add Child to node
     * @param Node $node
     * @return Node
     */
    function appendChild(Node $node){
        if($this->type === self::TYPE_DOCUMENT){
            $node->owner = $this;
        }else{
            $node->owner = $this->owner;
        }

        $node->parent = $this;

        $this->childNodes[] = $node;
        return $node;
    }
}