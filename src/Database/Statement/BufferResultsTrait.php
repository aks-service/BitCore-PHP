<?php
namespace Bit\Database\Statement;

/**
 * Contains a setter for marking a Statement as buffered
 *
 * @internal
 */
trait BufferResultsTrait
{

    /**
     * Whether or not to buffer results in php
     *
     * @var bool
     */
    protected $_bufferResults = true;

    /**
     * Whether or not to buffer results in php
     *
     * @param bool $buffer Toggle buffering
     * @return $this
     */
    public function bufferResults($buffer)
    {
        $this->_bufferResults = (bool)$buffer;
        return $this;
    }
}
