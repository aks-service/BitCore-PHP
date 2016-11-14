<?php
namespace Bit\Database;

use Bit\Database\Expression\Comparison;

/**
 * Sql dialect trait
 */
trait SqlDialectTrait
{

    /**
     * Quotes a database identifier (a column name, table name, etc..) to
     * be used safely in queries without the risk of using reserved words
     *
     * @param string $identifier The identifier to quote.
     * @return string
     */
    public function quoteIdentifier($identifier)
    {
        $identifier = trim($identifier);

        if ($identifier === '*') {
            return '*';
        }

        if ($identifier === '') {
            return '';
        }

        // string
        if (preg_match('/^[\w-]+$/', $identifier)) {
            return $this->_startQuote . $identifier . $this->_endQuote;
        }

        if (preg_match('/^[\w-]+\.[^ \*]*$/', $identifier)) {
// string.string
            $items = explode('.', $identifier);
            return $this->_startQuote . implode($this->_endQuote . '.' . $this->_startQuote, $items) . $this->_endQuote;
        }

        if (preg_match('/^[\w-]+\.\*$/', $identifier)) {
// string.*
            return $this->_startQuote . str_replace('.*', $this->_endQuote . '.*', $identifier);
        }

        if (preg_match('/^([\w-]+)\((.*)\)$/', $identifier, $matches)) {
// Functions
            return $matches[1] . '(' . $this->quoteIdentifier($matches[2]) . ')';
        }

        // Alias.field AS thing
        if (preg_match('/^([\w-]+(\.[\w-]+|\(.*\))*)\s+AS\s*([\w-]+)$/i', $identifier, $matches)) {
            return $this->quoteIdentifier($matches[1]) . ' AS ' . $this->quoteIdentifier($matches[3]);
        }

        if (preg_match('/^[\w-_\s]*[\w-_]+/', $identifier)) {
            return $this->_startQuote . $identifier . $this->_endQuote;
        }

        return $identifier;
    }

    /**
     * Returns an associative array of methods that will transform Expression
     * objects to conform with the specific SQL dialect. Keys are class names
     * and values a method in this class.
     *
     * @return array
     */
    protected function _expressionTranslators()
    {
        return [];
    }


    /**
     * Returns a SQL snippet for creating a new transaction savepoint
     *
     * @param string $name save point name
     * @return string
     */
    public function savePointSQL($name)
    {
        return 'SAVEPOINT LEVEL' . $name;
    }

    /**
     * Returns a SQL snippet for releasing a previously created save point
     *
     * @param string $name save point name
     * @return string
     */
    public function releaseSavePointSQL($name)
    {
        return 'RELEASE SAVEPOINT LEVEL' . $name;
    }

    /**
     * Returns a SQL snippet for rollbacking a previously created save point
     *
     * @param string $name save point name
     * @return string
     */
    public function rollbackSavePointSQL($name)
    {
        return 'ROLLBACK TO SAVEPOINT LEVEL' . $name;
    }
}
