<?php
namespace Bit\LessPHP;
use Bit\LessPHP\Interfaces\Less as LessInterface;
use Bit\Utility\Hash;

/**
 * Provides a registry/factory for Table objects.
 */
class LessMethod extends Less
{
    /**
     * @var array|null
     */
    private $_method = null;
    /**
     * @var array|null
     */
    private $param = null;

    function __construct(LessClass $parent,\ReflectionMethod $method = null)
    {
        //parent::__construct($parent);
        $this->_method = $method;
        $this->tags = Hash::merge($parent->tags, $method ? $this->parseDocBlock($method->getDocComment()) : []);
        /* TODO
        $params = array();
        foreach($method->getParameters() as $key=> &$param) {
            $params[$param->getName()] = (object)['param' => $param,
                'type'=> ($param->getClass() ? $param->getClass()->getName() : null),
                'passedbyRef'=>$param->isPassedByReference(),
                'allowNull'=>$param->allowsNull(),
                'pos' => $key
            ];
        }*/
    }

    function __debugInfo()
    {
        return parent::__debugInfo() + [
            'method'   => $this->_method
        ];
    }
}
