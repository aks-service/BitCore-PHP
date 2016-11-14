<?php
namespace Bit\Routing\Filter;

use Bit\Core\Plugin;
use Bit\Event\Event;
use Bit\Network\Request;
use Bit\Network\Response;
use Bit\Routing\DispatcherFilter;
use Bit\Utility\Inflector;

/**
 * Filters a request and tests whether it is a file in the webroot folder or not and
 * serves the file to the client if appropriate.
 *
 */
class AssetFilter extends DispatcherFilter
{

    /**
     * Default priority for all methods in this filter
     * This filter should run before the request gets parsed by router
     *
     * @var int
     */
    protected $_priority = 9;

    /**
     * The amount of time to cache the asset.
     *
     * @var string
     */
    protected $_cacheTime = '+1 day';

    /**
     *
     * Constructor.
     *
     * @param array $config Array of config.
     */
    public function __construct($config = [])
    {
        if (!empty($config['cacheTime'])) {
            $this->_cacheTime = $config['cacheTime'];
        }
        parent::__construct($config);
    }

    /**
     * Checks if a requested asset exists and sends it to the browser
     *
     * @param \Bit\Event\Event $event containing the request and response object
     * @return \Bit\Network\Response if the client is requesting a recognized asset, null otherwise
     * @throws \Bit\Network\Exception\NotFoundException When asset not found
     */
    public function beforeDispatch(Event $event)
    {
        $request = $event->data['request'];

        $url = urldecode($request->url);
        if (strpos($url, '..') !== false || strpos($url, '.') === false) {
            return null;
        }

        $assetFile = $this->_getAssetFile($url);
        if ($assetFile === null || !file_exists($assetFile)) {
            return null;
        }
        $response = $event->data['response'];
        $event->stopPropagation();

        $response->modified(filemtime($assetFile));
        if ($response->checkNotModified($request)) {
            return $response;
        }

        $pathSegments = explode('.', $url);
        $ext = array_pop($pathSegments);
        $this->_deliverAsset($request, $response, $assetFile, $ext);
        return $response;
    }

    /**
     * Builds asset file path based off url
     *
     * @param string $url Asset URL
     * @return string Absolute path for asset file
     */
    protected function _getAssetFile($url)
    {
        $parts = explode('/', $url);
        $pluginPart = [];
        for ($i = 0; $i < 2; $i++) {
            if (!isset($parts[$i])) {
                break;
            }
            $pluginPart[] = Inflector::camelize($parts[$i]);
            $plugin = implode('/', $pluginPart);
            if ($plugin && Plugin::loaded($plugin)) {
                $parts = array_slice($parts, $i + 1);
                $fileFragment = implode(DIRECTORY_SEPARATOR, $parts);
                $pluginWebroot = Plugin::path($plugin) . 'webroot' . DIRECTORY_SEPARATOR;
                return $pluginWebroot . $fileFragment;
            }
        }
    }

    /**
     * Sends an asset file to the client
     *
     * @param \Bit\Network\Request $request The request object to use.
     * @param \Bit\Network\Response $response The response object to use.
     * @param string $assetFile Path to the asset file in the file system
     * @param string $ext The extension of the file to determine its mime type
     * @return void
     */
    protected function _deliverAsset(Request $request, Response $response, $assetFile, $ext)
    {
        $compressionEnabled = $response->compress();
        if ($response->type($ext) === $ext) {
            $contentType = 'application/octet-stream';
            $agent = $request->env('HTTP_USER_AGENT');
            if (preg_match('%Opera(/| )([0-9].[0-9]{1,2})%', $agent) || preg_match('/MSIE ([0-9].[0-9]{1,2})/', $agent)) {
                $contentType = 'application/octetstream';
            }
            $response->type($contentType);
        }
        if (!$compressionEnabled) {
            $response->header('Content-Length', filesize($assetFile));
        }
        $response->cache(filemtime($assetFile), $this->_cacheTime);
        $response->sendHeaders();
        readfile($assetFile);
        if ($compressionEnabled) {
            ob_end_flush();
        }
    }
}
