<?php
namespace Bit\Console;

/**
 * Object wrapper for interacting with stdin
 *
 */
class ConsoleInput
{

    /**
     * Input value.
     *
     * @var resource
     */
    protected $_input;

    /**
     * Can this instance use readline?
     * Two conditions must be met:
     * 1. Readline support must be enabled.
     * 2. Handle we are attached to must be stdin.
     * Allows rich editing with arrow keys and history when inputting a string.
     *
     * @var bool
     */
    protected $_canReadline;

    /**
     * Constructor
     *
     * @param string $handle The location of the stream to use as input.
     */
    public function __construct($handle = 'php://stdin')
    {
        $this->_canReadline = extension_loaded('readline') && $handle === 'php://stdin' ? true : false;
        $this->_input = fopen($handle, 'r');
    }

    /**
     * Read a value from the stream
     *
     * @return mixed The value of the stream
     */
    public function read()
    {
        if ($this->_canReadline) {
            $line = readline('');
            if (strlen($line) > 0) {
                readline_add_history($line);
            }
            return $line;
        }
        return fgets($this->_input);
    }

    /**
     * Check if data is available on stdin
     *
     * @param int $timeout An optional time to wait for data
     * @return bool True for data available, false otherwise
     */
    public function dataAvailable($timeout = 0)
    {
        $readFds = [$this->_input];
        $readyFds = stream_select($readFds, $writeFds, $errorFds, $timeout);
        return ($readyFds > 0);
    }
}