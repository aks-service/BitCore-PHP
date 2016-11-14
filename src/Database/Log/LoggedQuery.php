<?php
namespace Bit\Database\Log;

/**
 * Contains a query string, the params used to executed it, time taken to do it
 * and the number of rows found or affected by its execution.
 *
 * @internal
 */
class LoggedQuery
{

    /**
     * Query string that was executed
     *
     * @var string
     */
    public $query = '';

    /**
     * Number of milliseconds this query took to complete
     *
     * @var float
     */
    public $took = 0;

    /**
     * Associative array with the params bound to the query string
     *
     * @var string
     */
    public $params = [];

    /**
     * Number of rows affected or returned by the query execution
     *
     * @var int
     */
    public $numRows = 0;

    /**
     * The exception that was thrown by the execution of this query
     *
     * @var \Exception
     */
    public $error;

    /**
     * Returns the string representation of this logged query
     *
     * @return string
     */
    public function __toString()
    {
        return "duration={$this->took} rows={$this->numRows} {$this->query}";
    }
}
