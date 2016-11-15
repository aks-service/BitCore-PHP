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
    /**
     * @var array|null
     */
    private $param = null;
    function __construct(LessInterface $parent,$method)
    {
        $this->_method = $method;
        $this->tags    = $this->parseDocBlock($method->getDocComment());

        $params = array();

        foreach($method->getParameters() as $key=> &$param) {
            $params[$param->getName()] = (object)['param' => $param,
                'type'=> ($param->getClass() ? $param->getClass()->getName() : null),
                'passedbyRef'=>$param->isPassedByReference(),
                'allowNull'=>$param->allowsNull(),
                'pos' => $key
            ];
        }

        parent::__construct($parent);
    }

    function __debugInfo()
    {
        return parent::__debugInfo() + [
            'method'   => $this->_method
        ];
    }
}
