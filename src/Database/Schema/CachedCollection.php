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

namespace Bit\Database\Schema;

use Bit\Cache\Cache;
use Bit\Database\ConnectionInterface;

/**
 * Extends the schema collection class to provide caching
 *
 */
class CachedCollection extends Collection
{

    /**
     * The name of the cache config key to use for caching table metadata,
     * of false if disabled.
     *
     * @var string|bool
     */
    protected $_cache = false;

    /**
     * Constructor.
     *
     * @param \Bit\Database\ConnectionInterface $connection The connection instance.
     * @param string|bool $cacheKey The cache key or boolean false to disable caching.
     */
    public function __construct(ConnectionInterface $connection, $cacheKey = true)
    {
        parent::__construct($connection);
        $this->cacheMetadata($cacheKey);
    }

    /**
     * {@inheritDoc}
     *
     * @param string $name
     * @param array $options
     * @return Table|mixed
     * @throws \Bit\Database\Exception
     */
    public function describe($name, array $options = [])
    {
        $options += ['forceRefresh' => false];
        $cacheConfig = $this->cacheMetadata();
        $cacheKey = $this->cacheKey($name);

        if (!empty($cacheConfig) && !$options['forceRefresh']) {
            $cached = Cache::read($cacheKey, $cacheConfig);
            if ($cached !== false) {
                return $cached;
            }
        }

        $table = parent::describe($name, $options);

        if (!empty($cacheConfig)) {
            Cache::write($cacheKey, $table, $cacheConfig);
        }

        return $table;
    }

    /**
     * Get the cache key for a given name.
     *
     * @param string $name The name to get a cache key for.
     * @return string The cache key.
     */
    public function cacheKey($name)
    {
        return $this->_connection->configName() . '_' . $name;
    }

    /**
     * Sets the cache config name to use for caching table metadata, or
     * disables it if false is passed.
     * If called with no arguments it returns the current configuration name.
     *
     * @param bool|null $enable whether or not to enable caching
     * @return string|bool
     */
    public function cacheMetadata($enable = null)
    {
        if ($enable === null) {
            return $this->_cache;
        }
        if ($enable === true) {
            $enable = '_bit_model_';
        }
        return $this->_cache = $enable;
    }
}
