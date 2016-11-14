<?php
namespace Bit\Log\Interfaces;

/**
 * LogStreamInterface is the interface that should be implemented
 * by all classes that are going to be used as Log streams.
 */
interface Log {

/**
 * Write method to handle writes being made to the Logger
 *
 * @param string $level The severity level of the message being written.
 *    See Bit\Log\Log::$_levels for list of possible levels.
 * @param string $message Message content to log
 * @param string|array $scope The scope(s) a log message is being created in.
 *    See Bit\Log\Log::config() for more information on logging scopes.
 * @return void
 */
	public function write($level, $message, $scope = []);
}
