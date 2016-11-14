<?php
/**
 * Created by PhpStorm.
 * User: bitcoding
 * Date: 22.04.16
 * Time: 15:28
 */

namespace Bit\LessPHP\Traits;


trait PHP
{
    use DocComment;

    private $this->_method = null;
    private $this->_tags   = null;
    public function getMethod($func = null){
        $method = $this->_getMethod($func);
        list(,$method,) = array_values((array)$method);
        $this->_method = $method;
        return $this;
    }

}