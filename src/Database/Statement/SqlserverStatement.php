<?php
namespace Bit\Database\Statement;

use PDO;

/**
 * Statement class meant to be used by an Sqlserver driver
 *
 * @internal
 */
class SqlserverStatement extends PDOStatement
{

    /**
     * The SQL Server PDO driver requires that binary parameters be bound with the SQLSRV_ENCODING_BINARY attribute.
     * This overrides the PDOStatement::bindValue method in order to bind binary columns using the required attribute.
     *
     * {@inheritDoc}
     */
    public function bindValue($column, $value, $type = 'string')
    {
        if ($type === null) {
            $type = 'string';
        }
        if (!ctype_digit($type)) {
            list($value, $type) = $this->cast($value, $type);
        }
        if ($type == PDO::PARAM_LOB) {
            $this->_statement->bindParam($column, $value, $type, 0, PDO::SQLSRV_ENCODING_BINARY);
        } else {
            $this->_statement->bindValue($column, $value, $type);
        }
    }
}
