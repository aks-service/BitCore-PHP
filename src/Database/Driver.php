<?php
namespace Bit\Database;

use InvalidArgumentException;
use PDO;

/**
 * Represents a database diver containing all specificities for
 * a database engine including its SQL dialect
 *
 */
abstract class Driver
{

    /**
     * Configuration data.
     *
     * @var array
     */
    protected $_config;

    /**
     * Base configuration that is merged into the user
     * supplied configuration data.
     *
     * @var array
     */
    protected $_baseConfig = [];

    /**
     * Indicates whether or not the driver is doing automatic identifier quoting
     * for all queries
     *
     * @var bool
     */
    protected $_autoQuoting = false;

    /**
     * Constructor
     *
     * @param array $config The configuration for the driver.
     * @throws \InvalidArgumentException
     */
    public function __construct($config = [])
    {
        if (empty($config['username']) && !empty($config['login'])) {
            throw new InvalidArgumentException(
                'Please pass "username" instead of "login" for connecting to the database'
            );
        }
        $config += $this->_baseConfig;
        $this->_config = $config;
        if (!empty($config['quoteIdentifiers'])) {
            $this->autoQuoting(true);
        }
    }

    /**
     * Establishes a connection to the database server
     *
     * @return bool true con success
     */
    abstract public function connect();

    /**
     * Disconnects from database server
     *
     * @return void
     */
    abstract public function disconnect();

    /**
     * Returns correct connection resource or object that is internally used
     * If first argument is passed,
     *
     * @param null|\PDO $connection The connection object
     * @return void
     */
    abstract public function connection($connection = null);

    /**
     * Returns whether php is able to use this driver for connecting to database
     *
     * @return bool true if it is valid to use this driver
     */
    abstract public function enabled();

    /**
     * Prepares a sql statement to be executed
     *
     * @param string|\Bit\Database\Query $query The query to convert into a statement.
     * @return \Bit\Database\StatementInterface
     */
    abstract public function prepare($query);

    /**
     * Starts a transaction
     *
     * @return bool true on success, false otherwise
     */
    abstract public function beginTransaction();

    /**
     * Commits a transaction
     *
     * @return bool true on success, false otherwise
     */
    abstract public function commitTransaction();

    /**
     * Rollsback a transaction
     *
     * @return bool true on success, false otherwise
     */
    abstract public function rollbackTransaction();

    /**
     * Get the SQL for releasing a save point.
     *
     * @param string $name The table name
     * @return string
     */
    abstract public function releaseSavePointSQL($name);

    /**
     * Get the SQL for creating a save point.
     *
     * @param string $name The table name
     * @return string
     */
    abstract public function savePointSQL($name);

    /**
     * Get the SQL for rollingback a save point.
     *
     * @param string $name The table name
     * @return string
     */
    abstract public function rollbackSavePointSQL($name);

    /**
     * Get the SQL for disabling foreign keys
     *
     * @return string
     */
    abstract public function disableForeignKeySQL();

    /**
     * Get the SQL for enabling foreign keys
     *
     * @return string
     */
    abstract public function enableForeignKeySQL();

    /**
     * Returns whether the driver supports adding or dropping constraints
     * to already created tables.
     *
     * @return bool true if driver supports dynamic constraints
     */
    abstract public function supportsDynamicConstraints();

    /**
     * Returns whether this driver supports save points for nested transactions
     *
     * @return bool true if save points are supported, false otherwise
     */
    public function supportsSavePoints()
    {
        return true;
    }

    /**
     * Returns a value in a safe representation to be used in a query string
     *
     * @param mixed $value The value to quote.
     * @param string $type Type to be used for determining kind of quoting to perform
     * @return string
     */
    abstract public function quote($value, $type);

    /**
     * Checks if the driver supports quoting
     *
     * @return bool
     */
    public function supportsQuoting()
    {
        return true;
    }

    /**
     * Get the schema dialect.
     *
     * Used by Bit\Database\Schema package to reflect schema and
     * generate schema.
     *
     * If all the tables that use this Driver specify their
     * own schemas, then this may return null.
     *
     * @return \Bit\Database\Schema\BaseSchema
     */
    abstract public function schemaDialect();

    /**
     * Quotes a database identifier (a column name, table name, etc..) to
     * be used safely in queries without the risk of using reserved words
     *
     * @param string $identifier The identifier expression to quote.
     * @return string
     */
    abstract public function quoteIdentifier($identifier);

    /**
     * Escapes values for use in schema definitions.
     *
     * @param mixed $value The value to escape.
     * @return string String for use in schema definitions.
     */
    public function schemaValue($value)
    {
        if ($value === null) {
            return 'NULL';
        }
        if ($value === false) {
            return 'FALSE';
        }
        if ($value === true) {
            return 'TRUE';
        }
        if (is_float($value)) {
            return str_replace(',', '.', strval($value));
        }
        if ((is_int($value) || $value === '0') || (
            is_numeric($value) && strpos($value, ',') === false &&
            $value[0] !== '0' && strpos($value, 'e') === false)
        ) {
            return $value;
        }
        return $this->_connection->quote($value, PDO::PARAM_STR);
    }

    /**
     * Returns last id generated for a table or sequence in database
     *
     * @param string|null $table table name or sequence to get last insert value from
     * @param string|null $column the name of the column representing the primary key
     * @return string|int
     */
    public function lastInsertId($table = null, $column = null)
    {
        return $this->_connection->lastInsertId($table, $column);
    }

    /**
     * Check whether or not the driver is connected.
     *
     * @return bool
     */
    public function isConnected()
    {
        return $this->_connection !== null;
    }

    /**
     * Returns whether or not this driver should automatically quote identifiers
     * in queries
     *
     * If called with a boolean argument, it will toggle the auto quoting setting
     * to the passed value
     *
     * @param bool|null $enable whether to enable auto quoting
     * @return bool
     */
    public function autoQuoting($enable = null)
    {
        if ($enable === null) {
            return $this->_autoQuoting;
        }
        return $this->_autoQuoting = (bool)$enable;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->_connection = null;
    }

    /**
     * Returns an array that can be used to describe the internal state of this
     * object.
     *
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'connected' => $this->isConnected()
        ];
    }
}
