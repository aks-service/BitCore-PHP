<?php
/**
 * Created by PhpStorm.
 * User: bitcoding
 * Date: 10.11.16
 * Time: 14:46
 */

namespace Bit\Controller;

use Bit\Core\Bit;
use Bit\LessPHP\Traits\LessPHP;
use Bit\PHPQuery\QueryObject;
use Bit\Traits\Statics;

trait QueryTrait
{
    use LessPHP;

    /**
     * Automatically set to the name of a plugin.
     *
     * @var string
     */
    public $plugin = null;

    /**
     * @var \Bit\LessPHP\LessMethod|null
     */
    public $method     = null;

    /**
     * @var QueryObject|null
     */
    protected $page = null;

    /**
     * Holds an array of paths.
     *
     * @var array
     */
    protected static $_paths = [];

    function paths($plugin = null, $cached = true,$path = "Template")
    {
        if ($cached === true) {
            if (!empty(static::$_paths[$plugin][$path])) {
                return static::$_paths[$plugin][$path];
            }
        }

        $templatePaths = Bit::path($path);
        $pluginPaths = $themePaths = [];
        if (!empty($plugin)) {
            for ($i = 0, $count = count($templatePaths); $i < $count; $i++) {
                $pluginPaths[] = $templatePaths[$i] . 'Plugin' . DIRECTORY_SEPARATOR . $plugin . DIRECTORY_SEPARATOR;
            }
            $pluginPaths = array_merge($pluginPaths, Bit::path($path, $plugin));
        }

        $paths = array_merge(
            $pluginPaths,
            $templatePaths,
            [dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Template' . DIRECTORY_SEPARATOR]
        );

        return static::$_paths[$plugin][$path] = $paths;
    }


    /**
     *
     * @return QueryObject|null
     */
    public function getTemplate($namespace) {
        $dirs = static::paths($this->plugin);
        $lang = Bit::getPreferredLanguage();
        $file = null;
        foreach($dirs as $dir){
            $dir = phi($dir.DS.$namespace.'.html');
            if (!is_file($file = $dir->dirname . DS . $lang . DS . $dir->basename)){
                if(!is_file($file = $dir->dirname . DS . $dir->basename)){
                    $file = null;
                }
            }

            if($file)
                return new QueryObject(file_get_contents($file)) ;
        }

        var_dump($dirs);

        die('Todo Exception GetTemplate '.$namespace);
        return null;
    }

    /**
     * @param $template
     * @param string|null $append
     * @param string|null $func
     * @return $this|self
     */
    public function loadTemplate($template, $append = null, $func = null) {
        $append =  $append ? :static::APPEND;
        $func = $func ? :static::APPEND_FUNC;

        $this->page->find($append)->$func($this->getTemplate($template));
        return $this;
    }


    /**
     * Returns whether there is an element at the specified offset.
     * This method is required by the interface ArrayAccess.
     * @param mixed the offset to check on
     * @return boolean
     */
    public function offsetExists($offset) {
        return true;
    }

    /**
     * Returns the element at the specified offset.
     * This method is required by the interface ArrayAccess.
     * @see QueryObject::find()
     * @return QueryObject
     */
    public function offsetGet($offset) {
        return $this->page->find($offset);
    }

    /**
     * Sets the element at the specified offset.
     * This method is required by the interface ArrayAccess.
     * @param string the offset to set element
     * @param mixed the element value
     */
    public function offsetSet($offset, $item) {
        $this->page->find($offset)->html($item);
    }

    /**
     * Unsets the element at the specified offset.
     * This method is required by the interface ArrayAccess.
     * @param string the offset to unset element
     */
    public function offsetUnset($offset) {
        $this->page->find($offset)->remove();
    }
}