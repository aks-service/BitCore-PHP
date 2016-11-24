<?php

namespace Bit\LessPHP\Traits;
use Bit\LessPHP\Reflector as Reflect;

trait Reflector{
    use DocComment;

    /**
     * @var null
     */
    protected $_reflect = null;

    /**
     *
     */
    public function reflect(){
        $this->_reflect = [];
        $class  = get_class($this);
        $pClass = get_parent_class($class);

        if(!isset($this->_reflect[$class]))
            $this->_reflect[$class]  = new \ReflectionClass($class);
        if(!isset($this->_reflect[$pClass]))
            $this->_reflect[$pClass] = new \ReflectionClass($pClass);

        $ClassReflector = &$this->_reflect[$class];
        $ParentReflector = &$this->_reflect[$pClass];

        return [$ClassReflector,$ParentReflector];
    }
}