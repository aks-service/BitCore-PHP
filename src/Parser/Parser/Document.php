<?php
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
     * @var string
     */
    public $type = self::TYPE_DOCUMENT;
    
    function createNewText($text){
        return new Text($text);
    }
    function createNewElement($name){
        return new Element($name);
    }
}