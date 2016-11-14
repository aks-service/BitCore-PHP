<?php
namespace Bit\LessPHP;
use Bit\LessPHP\Interfaces\Less as LessInterface;
/**
 * Provides a registry/factory for Table objects.
 */
class LessMethod extends Less
{
    /**
     * @var array|null
     */
    private $_method = null;

    function __construct(LessInterface $parent,$method)
    {
        $this->_method = $method;
        $this->tags    = $this->parseDocBlock($method->getDocComment());
        parent::__construct($parent);
    }

    function __debugInfo()
    {
        return parent::__debugInfo() + [
            'method'   => $this->_method
        ];
    }
}
