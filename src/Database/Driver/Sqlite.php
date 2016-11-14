<?php
namespace Bit\Database\Driver;

use Bit\Database\Dialect\SqliteDialectTrait;
use Bit\Database\Driver;
use Bit\Database\Query;
use Bit\Database\Statement\PDOStatement;
use Bit\Database\Statement\SqliteStatement;
use PDO;

class Sqlite extends Driver
{

    use PDODriverTrait;
    use SqliteDialectTrait;

    /**
     * Base configuration settings for Sqlite driver
     *
     * @var array
     */
    protected $_baseConfig = [
        'persistent' => false,
        'username' => null,
        'password' => null,
        'database' => ':memory:',
        'encoding' => 'utf8',
        'flags' => [],
        'init' => [],
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

        $dsn = "sqlite:{$config['database']}";
        $this->_connect($dsn, $config);

        if (!empty($config['init'])) {
            foreach ((array)$config['init'] as $command) {
                $this->connection()->exec($command);
            }
        }
        return true;
    }

    /**
     * Returns whether php is able to use this driver for connecting to database
     *
     * @return bool true if it is valid to use this driver
     */
    public function enabled()
    {
        return in_array('sqlite', PDO::getAvailableDrivers());
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
        $isObject = $query instanceof Query;
        $statement = $this->_connection->prepare($isObject ? $query->sql() : $query);
        $result = new SqliteStatement(new PDOStatement($statement, $this), $this);
        if ($isObject && $query->bufferResults() === false) {
            $result->bufferResults(false);
        }
        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsDynamicConstraints()
    {
        return false;
    }
}
