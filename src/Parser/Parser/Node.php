<?php
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
     * @var string
     */
    protected $name = "";

    /**
     * @var Node|Document|Text|Element|null
     */
    protected $parent = null;

    /**
     * @var string
     */
    public $type = self::TYPE_TEXT;
    
    /**
     * @var string
     */
    public $value = "";
    /**
     * @var bool
     */
    public $inline = false;

    function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * @var Node[]|null
     */
    protected $childNodes = [];

    /**
     * @var []|null
     */
    protected $attributes = null;
    /**
     * @param $name
     * @return mixed
     */
    function __get($name)
    {
        return $this->{$name};
    }

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