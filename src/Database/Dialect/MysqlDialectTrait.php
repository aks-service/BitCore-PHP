<?php
namespace Bit\Database\Dialect;

use Bit\Database\Schema\MysqlSchema;
use Bit\Database\SqlDialectTrait;

/**
 * Contains functions that encapsulates the SQL dialect used by MySQL,
 * including query translators and schema introspection.
 *
 * @internal
 */
trait MysqlDialectTrait
{

    use SqlDialectTrait;

    /**
     *  String used to start a database identifier quoting to make it safe
     *
     * @var string
     */
    protected $_startQuote = '`';

    /**
     * String used to end a database identifier quoting to make it safe
     *
     * @var string
     */
    protected $_endQuote = '`';

    /**
     * The schema dialect class for this driver
     *
     * @var \Bit\Database\Schema\MysqlSchema
     */
    protected $_schemaDialect;

    /**
     * Get the schema dialect.
     *
     * Used by Bit\Database\Schema package to reflect schema and
     * generate schema.
     *
     * @return \Bit\Database\Schema\MysqlSchema
     */
    public function schemaDialect()
    {
        if (!$this->_schemaDialect) {
            $this->_schemaDialect = new MysqlSchema($this);
        }
        return $this->_schemaDialect;
    }

    /**
     * {@inheritDoc}
     */
    public function disableForeignKeySQL()
    {
        return 'SET foreign_key_checks = 0';
    }

    /**
     * {@inheritDoc}
     */
    public function enableForeignKeySQL()
    {
        return 'SET foreign_key_checks = 1';
    }
}
