<?php
/**
 * Created by PhpStorm.
 * User: bitcoding
 * Date: 25.05.16
 * Time: 19:47
 */

namespace Bit\PHPQuery;


class PluginClone extends Plugin
{

    public function invoke(QueryObject $query,$args){
        $class = clone $this;
        $class->query($query);
        


        var_dump($query);
/*
        $class = clone $_cls;
        $class->query($this,$args);
        $class->invoke();
        var_dump($class);*/
        die();
        return $class;
    }



    /**
     * @var null
     */
    protected $_query=null;


    /**
     * @param QueryObject|null $object
     * @return QueryObject|null
     */
    public function query(QueryObject $object = null){
        if($object !== null) {
            $this->_query = $object;
        }
        return $this->_query;
    }
}