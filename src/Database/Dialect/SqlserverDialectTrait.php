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

namespace Bit\Database\Dialect;

use Bit\Database\Expression\FunctionExpression;
use Bit\Database\Expression\OrderByExpression;
use Bit\Database\Expression\UnaryExpression;
use Bit\Database\Query;
use Bit\Database\Schema\SqlserverSchema;
use Bit\Database\SqlDialectTrait;
use Bit\Database\SqlserverCompiler;
use PDO;

/**
 * Contains functions that encapsulates the SQL dialect used by SQLServer,
 * including query translators and schema introspection.
 *
 * @internal
 */
trait SqlserverDialectTrait
{

    use SqlDialectTrait;
    use TupleComparisonTranslatorTrait;

    /**
     * String used to start a database identifier quoting to make it safe
     *
     * @var string
     */
    protected $_startQuote = '[';

    /**
     * String used to end a database identifier quoting to make it safe
     *
     * @var string
     */
    protected $_endQuote = ']';

    /**
     * Modify the limit/offset to TSQL
     *
     * @param \Bit\Database\Query $query The query to translate
     * @return \Bit\Database\Query The modified query
     */
    protected function _selectQueryTranslator($query)
    {
        $limit = $query->clause('limit');
        $offset = $query->clause('offset');

        if ($limit && $offset === null) {
            $query->modifier(['_auto_top_' => sprintf('TOP %d', $limit)]);
        }

        if ($offset !== null && !$query->clause('order')) {
            $query->order($query->newExpr()->add('(SELECT NULL)'));
        }

        if ($this->_version() < 11 && $offset !== null) {
            return $this->_pagingSubquery($query, $limit, $offset);
        }

        return $this->_transformDistinct($query);
    }

    /**
     * Get the version of SQLserver we are connected to.
     *
     * @return int
     */
    public function _version()
    {
        $this->connect();
        return $this->_connection->getAttribute(PDO::ATTR_SERVER_VERSION);
    }

    /**
     * Generate a paging subquery for older versions of SQLserver.
     *
     * Prior to SQLServer 2012 there was no equivalent to LIMIT OFFSET, so a subquery must
     * be used.
     *
     * @param \Bit\Database\Query $original The query to wrap in a subquery.
     * @param int $limit The number of rows to fetch.
     * @param int $offset The number of rows to offset.
     * @return \Bit\Database\Query Modified query object.
     */
    protected function _pagingSubquery($original, $limit, $offset)
    {
        $field = '_bit_paging_._bit_page_rownum_';
        $order = $original->clause('order') ?: new OrderByExpression('(SELECT NULL)');

        $query = clone $original;
        $query->select([
                '_bit_page_rownum_' => new UnaryExpression('ROW_NUMBER() OVER', $order)
            ])->limit(null)
            ->offset(null)
            ->order([], true);

        $outer = new Query($query->connection());
        $outer->select('*')
            ->from(['_bit_paging_' => $query]);

        if ($offset) {
            $outer->where(["$field > " . (int)$offset]);
        }
        if ($limit) {
            $value = (int)$offset + (int)$limit;
            $outer->where(["$field <= $value"]);
        }

        // Decorate the original query as that is what the
        // end developer will be calling execute() on originally.
        $original->decorateResults(function ($row) {
            if (isset($row['_bit_page_rownum_'])) {
                unset($row['_bit_page_rownum_']);
            }
            return $row;
        });

        return $outer;
    }

    /**
     * Returns the passed query after rewriting the DISTINCT clause, so that drivers
     * that do not support the "ON" part can provide the actual way it should be done
     *
     * @param \Bit\Database\Query $original The query to be transformed
     * @return \Bit\Database\Query
     */
    protected function _transformDistinct($original)
    {
        if (!is_array($original->clause('distinct'))) {
            return $original;
        }

        $query = clone $original;
        $distinct = $query->clause('distinct');
        $query->distinct(false);

        $order = new OrderByExpression($distinct);
        $query
            ->select(function ($q) use ($distinct, $order) {
                $over = $q->newExpr('ROW_NUMBER() OVER')
                    ->add('(PARTITION BY')
                    ->add($q->newExpr()->add($distinct)->tieWith(','))
                    ->add($order)
                    ->add(')')
                    ->tieWith(' ');
                return [
                    '_bit_distinct_pivot_' => $over
                ];
            })
            ->limit(null)
            ->offset(null)
            ->order([], true);

        $outer = new Query($query->connection());
        $outer->select('*')
            ->from(['_bit_distinct_' => $query])
            ->where(['_bit_distinct_pivot_' => 1]);

        // Decorate the original query as that is what the
        // end developer will be calling execute() on originally.
        $original->decorateResults(function ($row) {
            if (isset($row['_bit_distinct_pivot_'])) {
                unset($row['_bit_distinct_pivot_']);
            }
            return $row;
        });

        return $outer;
    }

    /**
     * Returns a dictionary of expressions to be transformed when compiling a Query
     * to SQL. Array keys are method names to be called in this class
     *
     * @return array
     */
    protected function _expressionTranslators()
    {
        $namespace = 'Bit\Database\Expression';
        return [
            $namespace . '\FunctionExpression' => '_transformFunctionExpression',
            $namespace . '\TupleComparison' => '_transformTupleComparison'
        ];
    }

    /**
     * Receives a FunctionExpression and changes it so that it conforms to this
     * SQL dialect.
     *
     * @param \Bit\Database\Expression\FunctionExpression $expression The function expression to convert to TSQL.
     * @return void
     */
    protected function _transformFunctionExpression(FunctionExpression $expression)
    {
        switch ($expression->name()) {
            case 'CONCAT':
                // CONCAT function is expressed as exp1 + exp2
                $expression->name('')->tieWith(' +');
                break;
            case 'DATEDIFF':
                $hasDay = false;
                $visitor = function ($value) use (&$hasDay) {
                    if ($value === 'day') {
                        $hasDay = true;
                    }
                    return $value;
                };
                $expression->iterateParts($visitor);

                if (!$hasDay) {
                    $expression->add(['day' => 'literal'], [], true);
                }
                break;
            case 'CURRENT_DATE':
                $time = new FunctionExpression('GETUTCDATE');
                $expression->name('CONVERT')->add(['date' => 'literal', $time]);
                break;
            case 'CURRENT_TIME':
                $time = new FunctionExpression('GETUTCDATE');
                $expression->name('CONVERT')->add(['time' => 'literal', $time]);
                break;
            case 'NOW':
                $expression->name('GETUTCDATE');
                break;
            case 'EXTRACT':
                $expression->name('DATEPART')->tieWith(' ,');
                break;
            case 'DATE_ADD':
                $params = [];
                $visitor = function ($p, $key) use (&$params) {
                    if ($key === 0) {
                        $params[2] = $p;
                    } else {
                        $valueUnit = explode(' ', $p);
                        $params[0] = rtrim($valueUnit[1], 's');
                        $params[1] = $valueUnit[0];
                    }
                    return $p;
                };
                $manipulator = function ($p, $key) use (&$params) {
                    return $params[$key];
                };

                $expression
                    ->name('DATEADD')
                    ->tieWith(',')
                    ->iterateParts($visitor)
                    ->iterateParts($manipulator)
                    ->add([$params[2] => 'literal']);
                break;
            case 'DAYOFWEEK':
                $expression
                    ->name('DATEPART')
                    ->tieWith(' ')
                    ->add(['weekday, ' => 'literal'], [], true);
                break;
        }
    }

    /**
     * Get the schema dialect.
     *
     * Used by Bit\Schema package to reflect schema and
     * generate schema.
     *
     * @return \Bit\Database\Schema\MysqlSchema
     */
    public function schemaDialect()
    {
        return new SqlserverSchema($this);
    }

    /**
     * Returns a SQL snippet for creating a new transaction savepoint
     *
     * @param string $name save point name
     * @return string
     */
    public function savePointSQL($name)
    {
        return 'SAVE TRANSACTION t' . $name;
    }

    /**
     * Returns a SQL snippet for releasing a previously created save point
     *
     * @param string $name save point name
     * @return string
     */
    public function releaseSavePointSQL($name)
    {
        return 'COMMIT TRANSACTION t' . $name;
    }

    /**
     * Returns a SQL snippet for rollbacking a previously created save point
     *
     * @param string $name save point name
     * @return string
     */
    public function rollbackSavePointSQL($name)
    {
        return 'ROLLBACK TRANSACTION t' . $name;
    }

    /**
     * {@inheritDoc}
     *
     * @return \Bit\Database\SqlserverCompiler
     */
    public function newCompiler()
    {
        return new SqlserverCompiler();
    }

    /**
     * {@inheritDoc}
     */
    public function disableForeignKeySQL()
    {
        return 'EXEC sp_msforeachtable "ALTER TABLE ? NOCHECK CONSTRAINT all"';
    }

    /**
     * {@inheritDoc}
     */
    public function enableForeignKeySQL()
    {
        return 'EXEC sp_msforeachtable "ALTER TABLE ? WITH CHECK CHECK CONSTRAINT all"';
    }
}
