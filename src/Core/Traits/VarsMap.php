<?php
namespace Bit\Core\Traits;
use Bit\Core\VarsMap as Map;
/*
 * Represents a class that holds a TypeMap object
 */
trait VarsMap
{

    /**
     * @var \Bit\Core\VarsMap
     */
    protected $_varsMap;

    /**
     * Creates a new TypeMap if $varsMap is an array, otherwise returns the existing type map
     * or exchanges it for the given one.
     *
     * @param array|\Bit\Core\VarsMap|null $varsMap Creates a VarsMap if array, otherwise sets the given TypeMap
     * @return $this|\Bit\Core\VarsMap
     */
    public function varsMap($varsMap = null)
    {
        if ($this->_varsMap === null) {
            $this->_varsMap = new Map();
        }
        if ($varsMap === null) {
            return $this->_varsMap;
        }
        $this->_varsMap = is_array($varsMap) ? new Map($varsMap) : $varsMap;
        return $this;
    }

    /**
     * Allows setting default types when chaining query
     *
     * @param array|null $types The array of types to set.
     * @return $this|array
     */
    public function defaultTypes(array $types = null)
    {
        if ($types === null) {
            return $this->varsMap()->defaults();
        }
        $this->varsMap()->defaults($types);
        return $this;
    }
}
