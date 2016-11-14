<?php
namespace Bit\Traits;

/**
 * Class Statics
 * @package Bit\Traits
 */
trait Statics
{
    /**
     * @var null
     */
    private static $instance = null;

    /**
     * Statics constructor.
     */
    public function __construct($config = null){}


    /**
     * @param null|array $config
     * @return null|static|self
     */
    static function getStatic($config = null){
        if(!self::$instance)
            self::$instance = new static($config);
        return self::$instance;
    }
}