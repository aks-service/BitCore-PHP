<?php
namespace Bit\Database\Log;

use Bit\Log\Log;

/**
 * This class is a bridge used to write LoggedQuery objects into a real log.
 * by default this class use the built-in BitPHP Log class to accomplish this
 *
 * @internal
 */
class QueryLogger
{

    /**
     * Writes a LoggedQuery into a log
     *
     * @param \Bit\Database\Log\LoggedQuery $query to be written in log
     * @return void
     */
    public function log(LoggedQuery $query)
    {
        if (!empty($query->params)) {
            $query->query = $this->_interpolate($query);
        }
        $this->_log($query);
    }

    /**
     * Wrapper function for the logger object, useful for unit testing
     * or for overriding in subclasses.
     *
     * @param \Bit\Database\Log\LoggedQuery $query to be written in log
     * @return void
     */
    protected function _log($query)
    {
        Log::write('debug', $query, ['queriesLog']);
    }

    /**
     * Helper function used to replace query placeholders by the real
     * params used to execute the query
     *
     * @param \Bit\Database\Log\LoggedQuery $query The query to log
     * @return string
     */
    protected function _interpolate($query)
    {
        $params = array_map(function ($p) {
            if ($p === null) {
                return 'NULL';
            }
            if (is_bool($p)) {
                return $p ? '1' : '0';
            }
            return is_string($p) ? "'$p'" : $p;
        }, $query->params);

        $keys = [];
        $limit = is_int(key($params)) ? 1 : -1;
        foreach ($params as $key => $param) {
            $keys[] = is_string($key) ? "/:$key\b/" : '/[?]/';
        }

        return preg_replace($keys, $params, $query->query, $limit);
    }
}
