<?php
namespace Bit\Core;

use Bit\Core\Exception\WrongVarException;
use Bit\Core\Traits\InstanceConfig;
use Bit\Database\Driver;

use PDO;

/*
 * xxx:Interface
 */
class Vars{
    use InstanceConfig;
    
    /**
     * List of supported database types. A human readable
     * identifier is used as key and a complete namespaced class name as value
     * representing the class that will do actual type conversions.
     *
     * @var array
     */
    protected static $_types = [
        'biginteger' => 'Bit\Vars\Integer',
        'binary' => 'Bit\Vars\Binary',
        'boolean' => 'Bit\Vars\Boolean',
        'date' => 'Bit\Vars\Date',
        'datetime' => 'Bit\Vars\DateTime',
        'decimal' => 'Bit\Vars\Floated',
        'float' => 'Bit\Vars\Floated',
        'integer' => 'Bit\Vars\Integer',
        'string' => 'Bit\Vars\Text',
        'text' => 'Bit\Vars\Text',
        'time' => 'Bit\Vars\Time',
        'timestamp' => 'Bit\Vars\DateTime',
        'uuid' => 'Bit\Vars\UUID',
    ];
    
    /**
     * Returns a Type object capable of converting a type identified by $name
     *
     * @param string $name type identifier
     * @throws \InvalidArgumentException If type identifier is unknown
     * @return \Bit\Database\Type
     */
    public static function build($name,$val = null,$opts = [])
    {
        if (!isset(static::$_types[$name])) {
            throw new InvalidArgumentException(sprintf('Unknown type "%s"', $name));
        }
        if (is_string(static::$_types[$name])) {
            return new static::$_types[$name]($name,$val,$opts);
        }

        return null;
    }

    /**
     * Returns an arrays with all the mapped type objects, indexed by name
     *
     * @return array
     */
    public static function buildAll()
    {
        $result = [];
        foreach (self::$_types as $name => $type) {
            $result[$name] = static::build($name);
        }
        return $result;
    }

    /**
     * Registers a new type identifier and maps it to a fully namespaced classname,
     * If called with no arguments it will return current types map array
     * If $className is omitted it will return mapped class for $type
     *
     * @param string|array|\Bit\Database\Type|null $type if string name of type to map, if array list of arrays to be mapped
     * @param string|null $className The classname to register.
     * @return array|string|null if $type is null then array with current map, if $className is null string
     * configured class name for give $type, null otherwise
     */
    public static function map($type = null, $className = null)
    {
        if ($type === null) {
            return self::$_types;
        }
        if (is_array($type)) {
            self::$_types = $type;
            return null;
        }
        if ($className === null) {
            return isset(self::$_types[$type]) ? self::$_types[$type] : null;
        }
        self::$_types[$type] = $className;
    }

    /**
     * Clears out all created instances and mapped types classes, useful for testing
     *
     * @return void
     */
    public static function clear()
    {
        self::$_types = [];
        self::$_builtTypes = [];
    }
    
    
    
    /**
     * _defaultConfig
     *
     * Some default config
     *
     * @var array
     */
    protected $_defaultConfig = [
        'is'  => [],
        'get' => [],
        'to'  => []
    ];
    
    const SET = null;
    const IS = null;
    const GET = null;
    const CAST = null;
    
    protected $_value = null;
    
    /**
     * Identifier name for this type
     *
     * @var string
     */
    protected $_name = null;
    
    public function __construct($name = null,$var = null,$opts = []) {
        $this->_name = $name ? $name : get_called_class();
        $this->config($opts);
        $this->value($var,$opts);
    }
    
    
    public function value($val = null){
        if($val !== null){
            $this->_value = $val;
            return $this;
        }
        return $this->_value;
    }
    
    protected function _filter($method, $opts = []){
        return filter_var($this->value(), $method, $opts);
    }
    
    
    protected function _c($method, $opts = []){
        $opts += $this->config($method);
        $method = 'is' === $method   ? static::IS : static::GET;
        if(method_exists($this,$method)){
            $var = $this->{$method}($opts);
        }else{
            $var = $this->_filter($method,$opts);
        }
        
        return '' === $var ? null : $var;
    }
    
    public function is($opts = [])
    {
        return $this->_c('is',$opts) != null;
    }
    
    public function get($opts = [])
    {
        
        return $this->_c('get',$opts) != null;
    }


    /**
     * @param $var
     * @param array $opts
     * @return static|self
     */
    public static function make($var, $opts = []) {
        $cls = get_called_class();
        $val = new $cls($var,$opts);
        return $val;
    }
 
    //XXX: get method 
    public function __invoke($arg = null)
    {        
        if(is_array($arg)){
           return $this->get($arg); 
        }
        
        $args = func_get_args();
        array_shift($args);
        return $this->__call($arg,$args);
    }
    
    function __get($name) {
        return isset($this->{'_'.$name}) ? $this->{'_'.$name} : null ;
    }

    /**
     * Returns type identifier name for this object
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Returns the base type name that this class is inheriting.
     * This is useful when extending base type for adding extra functionality
     * but still want the rest of the framework to use the same assumptions it would
     * do about the base type it inherits from.
     *
     * @return string
     */
    public function getBaseType()
    {
        return $this->_name;
    }
}