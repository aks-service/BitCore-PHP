<?php
/**
 * Created by PhpStorm.
 * User: bitcoding
 * Date: 22.04.16
 * Time: 17:34
 */

namespace Bit\LessPHP;

use Bit\LessPHP\Interfaces\Less as LessInterface;

class LessClass extends Less
{
    /**
     * Less constructor.
     * @param \Bit\LessPHP\Interfaces\Less|null $parent
     * @param null $doc
     */
    function __construct(LessInterface &$parent = null)
    {
        $this->_parent = $parent;

        $comments = "";
        if($reflect = $parent->reflect())
            foreach($reflect as $comment ){
                $comments .= $comment->getDocComment();
            }
        $this->tags = $this->parseDocBlock($comments);
    }

    function getMethod($func)
    {
        return new LessMethod($this, method_exists($this->_parent, $func) ? new \ReflectionMethod($this->_parent, $func) : null);
    }
}