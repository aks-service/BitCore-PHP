<?php
namespace Bit\Database\Statement;

/**
 * Statement class meant to be used by an Sqlite driver
 *
 * @internal
 */
class SqliteStatement extends StatementDecorator
{

    use BufferResultsTrait;

    /**
     * {@inheritDoc}
     *
     */
    public function execute($params = null)
    {
        if ($this->_statement instanceof BufferedStatement) {
            $this->_statement = $this->_statement->getInnerStatement();
        }

        if ($this->_bufferResults) {
            $this->_statement = new BufferedStatement($this->_statement, $this->_driver);
        }

        return $this->_statement->execute($params);
    }

    /**
     * Returns the number of rows returned of affected by last execution
     *
     * @return int
     */
    public function rowCount()
    {
        if (preg_match('/^(?:DELETE|UPDATE|INSERT)/i', $this->_statement->queryString)) {
            $changes = $this->_driver->prepare('SELECT CHANGES()');
            $changes->execute();
            $count = $changes->fetch()[0];
            $changes->closeCursor();
            return (int)$count;
        }
        return parent::rowCount();
    }
}