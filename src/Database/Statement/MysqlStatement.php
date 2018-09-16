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
     *
     * @param null $params
     * @return bool
     */
    public function execute($params = null)
    {
        $this->_driver->connection()->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, $this->_bufferResults);
        $result = $this->_statement->execute($params);
        $this->_driver->connection()->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        return $result;
    }
}
