<?php
namespace Bit\Database;

/**
 * This interface defines the methods you can depend on in
 * a connection.
 */
interface ConnectionInterface
{
    /**
     * Get the configuration name for this connection.
     *
     * @return string
     */
    public function configName();

    /**
     * Get the configuration data used to create the connection.
     *
     * @return array
     */
    public function config();

    /**
     * Executes a callable function inside a transaction, if any exception occurs
     * while executing the passed callable, the transaction will be rolled back
     * If the result of the callable function is `false`, the transaction will
     * also be rolled back. Otherwise the transaction is committed after executing
     * the callback.
     *
     * The callback will receive the connection instance as its first argument.
     *
     * @param callable $transaction The callback to execute within a transaction.
     * @return mixed The return value of the callback.
     * @throws \Exception Will re-throw any exception raised in $callback after
     *   rolling back the transaction.
     */
    public function transactional(callable $transaction);

    /**
     * Run an operation with constraints disabled.
     *
     * Constraints should be re-enabled after the callback succeeds/fails.
     *
     * @param callable $operation The callback to execute within a transaction.
     * @return mixed The return value of the callback.
     * @throws \Exception Will re-throw any exception raised in $callback after
     *   rolling back the transaction.
     */
    public function disableConstraints(callable $operation);

    /**
     * Enables or disables query logging for this connection.
     *
     * @param bool|null $enable whether to turn logging on or disable it.
     *   Use null to read current value.
     * @return bool
     */
    public function logQueries($enable = null);

    /**
     * Sets the logger object instance. When called with no arguments
     * it returns the currently setup logger instance.
     *
     * @param object|null $instance logger object instance
     * @return object logger instance
     */
    public function logger($instance = null);
}
