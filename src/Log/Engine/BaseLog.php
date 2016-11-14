<?php
namespace Bit\Log\Engine;

use Bit\Core\Traits\InstanceConfig as InstanceConfigTrait;
use JsonSerializable;
use Psr\Log\AbstractLogger;

/**
 * Base log engine class.
 *
 */
abstract class BaseLog extends AbstractLogger
{

    use InstanceConfigTrait;

    /**
     * Default config for this class
     *
     * @var array
     */
    protected $_defaultConfig = [
        'levels' => [],
        'scopes' => []
    ];

    /**
     * __construct method
     *
     * @param array $config Configuration array
     */
    public function __construct(array $config = [])
    {
        $this->config($config);

        if (!is_array($this->_config['scopes']) && $this->_config['scopes'] !== false) {
            $this->_config['scopes'] = (array)$this->_config['scopes'];
        }

        if (!is_array($this->_config['levels'])) {
            $this->_config['levels'] = (array)$this->_config['levels'];
        }

        if (!empty($this->_config['types']) && empty($this->_config['levels'])) {
            $this->_config['levels'] = (array)$this->_config['types'];
        }
    }

    /**
     * Get the levels this logger is interested in.
     *
     * @return array
     */
    public function levels()
    {
        return $this->_config['levels'];
    }

    /**
     * Get the scopes this logger is interested in.
     *
     * @return array
     */
    public function scopes()
    {
        return $this->_config['scopes'];
    }

    /**
     * Converts to string the provided data so it can be logged. The context
     * can optionally be used by log engines to interpolate variables
     * or add additional info to the logged message.
     *
     * @param mixed $data The data to be converted to string and logged.
     * @param array $context Additional logging information for the message.
     * @return string
     */
    protected function _format($data, array $context = [])
    {
        if (is_string($data)) {
            return $data;
        }

        $object = is_object($data);

        if ($object && method_exists($data, '__toString')) {
            return (string)$data;
        }

        if ($object && $data instanceof JsonSerializable) {
            return json_encode($data);
        }

        return print_r($data, true);
    }
}
