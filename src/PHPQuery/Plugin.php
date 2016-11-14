<?php
namespace Bit\PHPQuery;

use Bit\Core\Traits\InstanceConfig;
use Bit\Traits\Statics;

/**
 * Class Plugin
 * @package Bit\PHPQuery
 */
class Plugin
{
    use Statics;
    use InstanceConfig;


    /**
     * Default config for this class
     *
     * @var array
     */
    protected $_defaultConfig = [
    ];


    public function __construct(array $config = [])
    {
        $this->config($config);
   }

    public function invoke(QueryObject $query,$args){
        return $query;
    }
}