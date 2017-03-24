<?php

namespace Bit\LessPHP\Traits;
use Bit\LessPHP\Reflector as Reflect;

trait Reflector{
    use DocComment;

    /**
     * @var null
     */
    protected $_reflect = [];


    /**
     * @return \ReflectionClass[]|null
     */
    public function reflect(){
        if(!empty($this->_reflect))
            return null;
        
        $class  = get_class($this);

        if(!isset($this->_reflect[$class]))
            $this->_reflect[$class]  = new \ReflectionClass($class);

        while($pClass = get_parent_class($class)){
            $this->_reflect[$pClass] = new \ReflectionClass($pClass);
            $class = $pClass;
        }

        return empty($this->_reflect) ? null : $this->_reflect ;
    }
}