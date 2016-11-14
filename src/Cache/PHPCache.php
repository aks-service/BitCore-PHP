<?php
    namespace Bit\Cache;
/**
 * Simple php cache using var_export generated files
 * 
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class PHPCache {
    
    const DEFAULT_TTL = 3600;
    
    /**
     * @var string
     */
    protected $cacheDir;
    
    /**
     * @var int
     */
    protected $defaultTtl;
    
    /**
     * @param string $cacheDir where to store cache files
     */
    public function __construct($cacheDir, $ttl = self::DEFAULT_TTL) {
        $cacheDir = realpath($cacheDir);
        if(!$cacheDir) {
            throw new InvalidArgumentException('Provided cache dir does not exist');
        }
        if(!is_dir($cacheDir)) {
            throw new InvalidArgumentException('Provided cache dir is not a directory');
        }
        if(!(is_readable($cacheDir) && is_writable($cacheDir))) {
            throw new InvalidArgumentException('Provided cache dir is not writable and readable');
        }
        $this->cacheDir = $cacheDir;
        $this->defaultTtl = (int) $ttl;
    }
    
    public function read($key) {
        $key = (string) $key;
        $hash = md5($key);
        $fileName = $this->cacheDir . DIRECTORY_SEPARATOR . $hash . '.php';
        $cached = (include($fileName));
       
        if(
            $cached
            && isset($cached['key'])
            && isset($cached['hash'])
            && isset($cached['timestamp'])
            && isset($cached['ttl'])
            && isset($cached['value'])
            && ((time() - $cached['timestamp']) < $cached['ttl'])
            && $cached['key'] === $key
            && $cached['hash'] === $hash
        ) {
            return $cached['value'];
        }
        if($cached) {
            @unlink($fileName);
        }
        return false;
    }
    
    public function write($key, $value, $ttl = null) {
        $ttl = $ttl > 0 ? $ttl : $this->defaultTtl;
        $key = (string) $key;
        $hash = md5($key);
        $saved = file_put_contents(
            $this->cacheDir . DIRECTORY_SEPARATOR . $hash . '.php', 
            '<?php return ' .str_replace('stdClass::', '\Bit\Vars\stdClass::', var_export(
                array(
                    'key'       => $key,
                    'hash'      => $hash,
                    'value'     => $value,
                    'timestamp' => time(),
                    'ttl'       => $ttl,
                ),
                true
            ))  . ';'
        );
    }
}