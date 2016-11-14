<?php
namespace Bit\Database\Schema;

use Bit\Cache\Cache;
use Bit\Datasource\ConnectionInterface;

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
     * @param \Bit\Datasource\ConnectionInterface $connection The connection instance.
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
