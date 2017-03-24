<?php
/* 
 * BitCore (tm) : Bit Development Framework
 * Copyright (c) BitCore
 * 
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 * 
 * @copyright     BitCore
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Bit\Vars;
use Bit\Core\Vars;

abstract class Flag extends Enum{
    function __construct($var)
    {
        if(is_array($var))
        {
            $constants = $this->getConstList();
            $d = 0;
            foreach ($var as $key){
                $d |= $constants[$key];
            }
            $var = $d;
        }else if(is_string($var) && !is_numeric($var))
        {
            if((strpos($var,'|') !== false))
                $var = explode('|',$var);
            else
                $var = [$var];

            $constants = $this->getConstList();
            $d = 0;
            foreach ($var as $key){
                $d |= $constants[$key];
            }
            $var = $d;
        }
        else if(is_string($var))
        {
            $var = intval($var);
        }
        parent::__construct($var);
    }

    public function hasFlag($flag)
    {
        if (is_numeric($flag))
            return $this->_value & $flag;
        elseif(is_string($flag)){
            $const = $this->getConstList();
            return isset($const[$flag]) ? $this->_value & $const[$flag] : 0 ;
        }
        return 0;
    }

    public function get($object = false){
        if(!$object)
            return parent::get();
        
        $const = $this->getConstList();
        $value = $this->_value;
        $call = function($val) use ($value){
            return ($val & $value) ? $val : 0;
        };
        
        return  (object)array_combine(
                    array_keys($const), 
                    array_map($call, $const)
                );
    }

    function __toString()
    {
        // TODO: Implement __toString() method.
        $flags = (array)$this->get("true");
        $ret = "";
        $i = 0;
        foreach ($flags as $name=>$value){
            if(!$value)
                continue;
            if($i++)
                $ret .= " | ";
            $ret.= $name;
        }
        return $ret;
    }
}