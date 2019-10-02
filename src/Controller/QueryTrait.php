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

namespace Bit\Controller;

use Bit\Controller\Exception\MissingScriptException;
use Bit\Core\Bit;
use Bit\LessPHP\Traits\LessPHP;
use Bit\PHPQuery\QueryObject;
use Bit\Traits\Statics;
use Bit\Controller\Exception\MissingTemplateException;

/**
 * Trait QueryTrait
 * @package Bit\Controller
 */
trait QueryTrait
{
    use LessPHP;

    /**
     * Variables for the view
     *
     * @var array
     */
    public $viewVars = [];

    /**
     * Automatically set to the name of a plugin.
     *
     * @var string
     */
    public $plugin = null;

    /**
     * method holder
     * @var \Bit\LessPHP\LessMethod|null
     */
    public $method     = null;

    /**
     * page holder like document in javascript
     * @var QueryObject|null
     */
    protected $page = null;

    /**
     * Holds an array of paths.
     *
     * @var array
     */
    protected static $_paths = [];

    /**
     * Return Template Paths from
     *
     * @param null $plugin
     * @param bool $cached
     * @param string $path
     * @return array
     */
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
     * Get Template File
     *
     * @param $template
     * @return QueryObject|null
     */
    public function getTemplate($template) {
        $dirs = static::paths($this->plugin);
        $lang = Bit::getPreferredLanguage();
        $file = null;
        foreach($dirs as $dir){
            $dir = phi($dir.DS.$template.'.html');
            if (!is_file($file = $dir->dirname . DS . $lang . DS . $dir->basename)){
                if(!is_file($file = $dir->dirname . DS . $dir->basename)){
                    $file = null;
                }
            }

            if($file)
                return new QueryObject(file_get_contents($file)) ;
        }

        throw new MissingTemplateException([
            'template' => $template.'.html',
            'dirs'   => $dirs,
        ]);

        return null;
    }

    /**
     * Load Template in Document
     *
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
     *
     * @param mixed $offset the offset to check on
     * @return boolean
     */
    public function offsetExists($offset) {
        return true;
    }

    /**
     * Returns the element at the specified offset.
     * This method is required by the interface ArrayAccess.
     *
     * @see QueryObject::find()
     * @param mixed $offset the offset to check on
     * @return QueryObject
     */
    public function offsetGet($offset) {
        return $this->page->find($offset);
    }

    /**
     * Sets the element at the specified offset.
     * This method is required by the interface ArrayAccess.
     * @param string $offset the offset to set element
     * @param mixed the element value
     */
    public function offsetSet($offset, $item) {
        $this->page->find($offset)->html($item);
    }

    /**
     * Unsets the element at the specified offset.
     * This method is required by the interface ArrayAccess.
     * @param string $offset the offset to unset element
     */
    public function offsetUnset($offset) {
        $this->page->find($offset)->remove();
    }

    /**
     * Clear Title and Set new One Be CareFull
     * @param string $title the new Title
     * @return void
     */
    public function setTitle($title) {
        $this->page->find('title')->text($title);
    }

    /**
     * Append Title at the End
     * @param string $title the new Title
     * @param string $key Delemiter default  »
     * @param string $func Functions default  append
     * @return void
     */
    public function addTitle($title, $key = ' » ',$func = 'append') {
        $_title = $this->page->find('title')->text();
        $this->page->find('title')->text($func === 'append' ? $_title.$key . $title : $title.$key.$_title );
    }

    /**
     * Saves a variable or an associative array of variables for use inside a template.
     *
     * @param string|array $name A string or an array of data.
     * @param mixed $value Value in case $name is a string (which then works as the key).
     *   Unused if $name is an associative array, otherwise serves as the values to $name's keys.
     * @return $this
     */
    public function set($name, $value = null)
    {
        if (is_array($name)) {
            if (is_array($value)) {
                $data = array_combine($name, $value);
            } else {
                $data = $name;
            }
        } else {
            $data = [$name => $value];
        }
        $this->viewVars = $data + $this->viewVars;
        return $this;
    }

    /**
     * Scripted Template
     *
     * @param $template
     * @param string $ext
     */
    public function script($template, $args = null, $ext = '.ptp'){
        $dirs = static::paths($this->plugin);

        $lang = Bit::getPreferredLanguage();
        $file = null;
        foreach($dirs as $_dir){
            $dir = phi($_dir.DS.$template.$ext);

            if (!is_file($file = $dir->dirname . DS . $lang . DS . $dir->basename) && !is_file($file = $dir->dirname . DS . $dir->basename)){
                    $file = null;
            }
            if($file){
                extract(is_array($args)?$args:$this->viewVars);
                try {
                    return include $file;
                }catch(\Exception $e){
                }
            }
        }
        throw new MissingScriptException([
            'script' => $template,
            'dirs'   => $dirs,
        ]);

    }
}
