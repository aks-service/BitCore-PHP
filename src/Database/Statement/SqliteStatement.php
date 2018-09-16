<?php
/**
 * BitCore-PHP:  Rapid Development Framework (https://phpcore.bitcoding.eu)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          https://phpcore.bitcoding.eu BitCore-PHP Project
 * @since         0.7.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

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
     * @param null $params
     * @return bool
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
     * {@inheritDoc}
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
