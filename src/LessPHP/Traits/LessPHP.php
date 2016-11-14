<?php
/**
 * Created by PhpStorm.
 * User: bitcoding
 * Date: 22.04.16
 * Time: 17:35
 */

namespace Bit\LessPHP\Traits;
use Bit\LessPHP\LessClass;

trait LessPHP
{
    use Reflector;
    /**
     * @var LessClass|null
     */
    private $_less = null;

    /**
     * @return LessClass|null
     */
    public function less(){
        if($this->_less === null)
            $this->_less = new LessClass($this);
        return $this->_less;
    }
}