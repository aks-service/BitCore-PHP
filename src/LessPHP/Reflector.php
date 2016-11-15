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
            $this->_methods[$reflectmethod->getName()] = $reflectmethod;
        }
    }
    function __get($name) {
        return isset($this->{'_'.$name}) ? $this->{'_'.$name} : null;
    }
    
    function toArray(){
        
    }
}