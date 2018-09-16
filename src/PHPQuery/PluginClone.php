<?php
/**
 * BitCore-PHP:  Rapid Development Framework (https://phpcore.bitcoding.eu)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          https://phpcore.bitcoding.eu BitCore-PHP Project
 * @since         0.2.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Bit\PHPQuery;

/**
 * Class PluginClone
 * @package Bit\PHPQuery
 */
class PluginClone extends Plugin
{

    /**
     * Invoke Clone
     *
     * @param QueryObject $query
     * @param $args
     * @return PluginClone|QueryObject
     */
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
     * Query
     * @var null
     */
    protected $_query=null;


    /**
     * Query
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