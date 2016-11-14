<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Bit\LessPHP\Traits;

use Bit\LessPHP;

trait LessVar{
    
    /**
    * Variables for the LessVar
    *
    * @var array
    */
    public $lessVars = [];
    /**
    * Variables for the LessVar
    *
    * @var array
    */
    static protected $_lessGlobalVars = [];
    
    
    
    /**
     * Get variable for use.
     * @param array &$_var Data Array.
     * @param string|array $name A string or an array of data.
     * @param string|array $class Cast To VarClass
     * @param string|array $args  Cast To VarClass args
     * @return mixed|Vars|Object
     */
    protected static function _getVar(Array &$_var,$name = null,$class=null,$args = null){
        $default = ['class'=>null,'args'=> null];
        if (is_array($name)) {
            $result = [];
            foreach($name as $key => $val){
                $opts = (is_string($val) ? ['class'=>$val] : (is_array($val) ? $val : [])) + ['var'=> (is_string($key) ? $key : null)];
                $opts += $default; 
                $class = $opts['class'];
                $result[$key] = isset($_var[$opts['var']]) ? ($class ? new $class($_var[$opts['var']],$opts['args']) : $_var[$opts['var']])  : null;
            }
            return $result;
        } 
        //class_exists('Vars\\'.$class, true);
        return isset($_var[$name]) ? ($class ? new $class($_var[$name],$args) : $_var[$name])  : $_var;
    }
    
    /**
     * Saves a variable for use inside a Var.
     * @param array &$_var Data Array.
     * @param string|array $name A string or an array of data.
     * @param string|array $val Value in case $name is a string (which then works as the key).
     *   Unused if $name is an associative array, otherwise serves as the values to $name's keys.
     * @return void
     */
    protected static function _setVar(Array &$var,$name, $val = null){
        $var = ((is_array($name)) ? ((is_array($val)) ? array_combine($name, $val) : $name) : [$name => $val]) + $var;
    }
    /**
     * @see LessVar::_getVar()
     */
    public function getVar($name = null,$class=null,$args = null) {
       return self::_getVar($this->lessVars,$name,$class,$args);
    }
    /**
     * @see LessVar::_setVar()
     * @return $this
     */
    public function setVar($name, $val = null) {
        self::_setVar($this->lessVars,$name, $val);
        return $this;
    }
    /**
     * @see LessVar::_getVar
     */
    public static function getGlobalVar($name = null,$class=null,$args = null) {
        return self::_getVar(self::_lessGlobalVars,$name,$class,$args);
    }
    
    
    /**
     * @see LessVar::_setVar
     */
    public static function setGlobalVar($name, $val = null) {
        self::_setVar(self::_lessGlobalVars,$name, $val);
    }
}