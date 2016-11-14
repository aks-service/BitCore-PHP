<?php
namespace Bit\Database\Driver;

use Bit\Database\Dialect\SqlserverDialectTrait;
use Bit\Database\Driver;
use Bit\Database\Query;
use Bit\Database\Statement\SqlserverStatement;
use PDO;

/**
 * SQLServer driver.
 */
class Sqlserver extends Driver
{

    use PDODriverTrait;
    use SqlserverDialectTrait;

    /**
     * Base configuration settings for Sqlserver driver
     *
     * @var array
     */
    protected $_baseConfig = [
        'persistent' => false,
        'host' => 'localhost\SQLEXPRESS',
        'username' => '',
        'password' => '',
        'database' => 'bit',
        // PDO::SQLSRV_ENCODING_UTF8
        'encoding' => 65001,
        'flags' => [],
        'init' => [],
        'settings' => [],
    ];

    /**
     * Establishes a connection to the database server
     *
     * @return bool true on success
     */
    public function connect()
    {
        if ($this->_connection) {
            return true;
        }
        $config = $this->_config;
        $config['flags'] += [
            PDO::ATTR_PERSISTENT => $config['persistent'],
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        if (!empty($config['encoding'])) {
            $config['flags'][PDO::SQLSRV_ATTR_ENCODING] = $config['encoding'];
        }

        $dsn = "sqlsrv:Server={$config['host']};Database={$config['database']};MultipleActiveResultSets=false";
        $this->_connect($dsn, $config);

        $connection = $this->connection();
        if (!empty($config['init'])) {
            foreach ((array)$config['init'] as $command) {
                $connection->exec($command);
            }
        }
        if (!empty($config['settings']) && is_array($config['settings'])) {
            foreach ($config['settings'] as $key => $value) {
                $connection->exec("SET {$key} {$value}");
            }
        }
        return true;
    }

    /**
     * Returns whether PHP is able to use this driver for connecting to database
     *
     * @return bool true if it is valid to use this driver
     */
    public function enabled()
    {
        return in_array('sqlsrv', PDO::getAvailableDrivers());
    }

    /**
     * Prepares a sql statement to be executed
     *
     * @param string|\Bit\Database\Query $query The query to prepare.
     * @return \Bit\Database\StatementInterface
     */
    public function prepare($query)
    {
        $this->connect();
        $options = [PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL];
        $isObject = $query instanceof Query;
        if ($isObject && $query->bufferResults() === false) {
            $options = [];
        }
        $statement = $this->_connection->prepare($isObject ? $query->sql() : $query, $options);
        return new SqlserverStatement($statement, $this);
    }

    /**
     * {@inheritDoc}
     */
    public function supportsDynamicConstraints()
    {
        return true;
    }
}
