<?php
namespace Bit\Database\Statement;

use PDO;

/**
 * Statement class meant to be used by a Mysql PDO driver
 *
 * @internal
 */
class MysqlStatement extends PDOStatement
{

    use BufferResultsTrait;

    /**
     * {@inheritDoc}
     *
     */
    public function execute($params = null)
    {
        $this->_driver->connection()->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, $this->_bufferResults);
        $result = $this->_statement->execute($params);
        $this->_driver->connection()->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        return $result;
    }
}
