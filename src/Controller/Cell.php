<?php
namespace Bit\Controller;

use BadMethodCallException;
use Bit\Cache\Cache;
use Bit\Controller\Exception\MissingCellViewException;
use Bit\Controller\Exception\MissingTemplateException;
use Bit\Event\EventDispatcherTrait;
use Bit\Event\EventManager;
use Bit\Network\Request;
use Bit\Network\Response;
use Bit\PHPQuery\QueryObject;
use Bit\Utility\Inflector;

use Bit\LessPHP\Interfaces\Less as LessInterface;

use Exception;
use ReflectionException;
use ReflectionMethod;

/**
 * Cell base.
 *
 */
abstract class Cell implements \ArrayAccess, LessInterface
{
    CONST APPEND = 'body';
    CONST APPEND_FUNC  = 'append';

    use QueryTrait;
    use EventDispatcherTrait;

    /**
     * Name of the template that will be rendered.
     * This property is inflected from the action name that was invoked.
     *
     * @var string
     */
    public $template;


    /**
     * An instance of a Bit\Network\Request object that contains information about the current request.
     * This object contains all the information about a request and several methods for reading
     * additional information about the request.
     *
     * @var \Bit\Network\Request
     */
    public $request;

    /**
     * An instance of a Response object that contains information about the impending response
     *
     * @var \Bit\Network\Response
     */
    public $response;

    /**
     * The helpers this cell uses.
     *
     * This property is copied automatically when using the CellTrait
     *
     * @var array
     */
    public $helpers = [];

    /**
     * The cell's action to invoke.
     *
     * @var string
     */
    public $action;

    /**
     * Arguments to pass to cell's action.
     *
     * @var array
     */
    public $args = [];

    /**
     * List of valid options (constructor's fourth arguments)
     * Override this property in subclasses to whitelist
     * which options you want set as properties in your Cell.
     *
     * @var array
     */
    protected $_validCellOptions = [];

    /**
     * Caching setup.
     *
     * @var array|bool
     */
    protected $_cache = false;

    /**
     * Constructor.
     *
     * @param \Bit\Network\Request|null $request The request to use in the cell.
     * @param \Bit\Network\Response|null $response The response to use in the cell.
     * @param \Bit\Event\EventManager|null $eventManager The eventManager to bind events to.
     * @param array $cellOptions Cell options to apply.
     */
    public function __construct(
        Request $request = null,
        Response $response = null,
        EventManager $eventManager = null,
        array $cellOptions = []
    ) {
        $this->eventManager($eventManager);
        $this->request = $request;
        $this->response = $response;

        $this->_validCellOptions = array_merge(['action', 'args'], $this->_validCellOptions);
        foreach ($this->_validCellOptions as $var) {
            if (isset($cellOptions[$var])) {
                $this->{$var} = $cellOptions[$var];
            }
        }
        if (!empty($cellOptions['cache'])) {
            $this->_cache = $cellOptions['cache'];
        }
    }


    /**
     * Render the cell.
     *
     * @param callable|null $call
     * @return QueryObject|string
     *
     * @throws \Bit\Controller\Exception\MissingCellViewException When a MissingTemplateException is raised during rendering.
     */
    public function render(callable $call = null)
    {
        $cache = [];
        if ($this->_cache) {
            $cache = $this->_cacheConfig($this->action);
        }

        $render = function () use ($call) {
            try {
                $reflect = new ReflectionMethod($this, $this->action);
                $this->page = new QueryObject('<body/>') ;
                $this->method = $this->less()->getMethod($this->action);

                array_map(function ($params) {
                    call_user_func_array([$this, "loadTemplate"], $params);
                }, $this->method->getTag('template'));

                $reflect->invokeArgs($this, $this->args);

            } catch (ReflectionException $e) {
                throw new BadMethodCallException(sprintf(
                    'Class %s does not have a "%s" method.',
                    get_class($this),
                    $this->action
                ));
            }

            $className = get_class($this);
            $namePrefix = '\Controller\Cell\\';
            $name = substr($className, strpos($className, $namePrefix) + strlen($namePrefix));
            $name = substr($name, 0, -4);

            $dd = $this->page->find('body > *');
            
            if($call)
                $call($dd);

            return $dd;
        };

        if ($cache) {
            return Cache::remember($cache['key'], $render, $cache['config']);
        }
        return $render();
    }

    /**
     * Generate the cache key to use for this cell.
     *
     * If the key is undefined, the cell class and action name will be used.
     *
     * @param string $action The action invoked.
     * @return array The cache configuration.
     */
    protected function _cacheConfig($action)
    {
        if (empty($this->_cache)) {
            return [];
        }
        $key = 'cell_' . Inflector::underscore(get_class($this)) . '_' . $action;
        $key = str_replace('\\', '_', $key);
        $default = [
            'config' => 'default',
            'key' => $key
        ];
        if ($this->_cache === true) {
            return $default;
        }
        return $this->_cache + $default;
    }

    /**
     * Magic method.
     *
     * Starts the rendering process when Cell is echoed.
     *
     * *Note* This method will trigger an error when view rendering has a problem.
     * This is because PHP will not allow a __toString() method to throw an exception.
     *
     * @return string Rendered cell
     */
    public function __toString()
    {
        try {
            return $this->render();
        } catch (Exception $e) {
            trigger_error('Could not render cell - ' . $e->getMessage(), E_USER_WARNING);
            return '';
        }
    }

    /**
     * Debug info.
     *
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'plugin' => $this->plugin,
            'action' => $this->action,
            'args' => $this->args,
            'template' => $this->template,
            'request' => $this->request,
            'response' => $this->response,
        ];
    }
}
