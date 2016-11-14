<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Bit\LessPHP;

/**
 * @see \ReflectionClass
 */
class Reflector extends \ReflectionClass{
    private $_methods;
    //private $_consts;
    
    function __construct($argument) {
        parent::__construct($argument);
        
        foreach($this->getMethods() as $reflectmethod) {
            $method = &$this->_methods[$reflectmethod->getName()];
            $params = array();
            
            foreach($reflectmethod->getParameters() as $key=> &$param) {
                $params[$param->getName()] = (object)['param' => $param, 
                    'type'=> ($param->getClass() ? $param->getClass()->getName() : null),
                    'passedbyRef'=>$param->isPassedByReference(),
                    'allowNull'=>$param->allowsNull(),
                    'pos' => $key
                    ];
            }        
    
            $method = (object)['func'=>$reflectmethod->getName(),'method' => $reflectmethod,'params'=>$params];
        }
    }
    function __get($name) {
        return isset($this->{'_'.$name}) ? $this->{'_'.$name} : null;
    }
    
    function toArray(){
        
    }
}