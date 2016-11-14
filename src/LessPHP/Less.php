<?php
/**
 * Created by PhpStorm.
 * User: bitcoding
 * Date: 22.04.16
 * Time: 17:33
 */

namespace Bit\LessPHP;
use Bit\LessPHP\Interfaces\Less as LessInterface;
use Bit\LessPHP\Traits\DocComment;

abstract class Less
{
    use DocComment;
    /**
     * @var bool
     */
    static  $cacheit = true;
    /**
     * @var \Bit\LessPHP\Interfaces\Less|null
     */
    private $_parent = null;

    /**
     * @var array|null
     */
    public $tags = [];
    /**
     * @var array|null
     */
    private $_methods = null;

    /**
     * Less constructor.
     * @param \Bit\LessPHP\Interfaces\Less|null $parent
     * @param null $doc
     */
    function __construct(LessInterface &$parent = null) {
        if (!($parent instanceof LessInterface)) {
            //XXX:Todo
        }

        $this->_parent = $parent;
        list($ClassReflector,$ParentReflector) = $parent->reflect();

        $this->tags = array_merge($this->tags,$this->parseDocBlock($ClassReflector->getDocComment()));
        $this->_methods = array_diff_key($ClassReflector->methods, $ParentReflector->methods);
    }

    function getMethod($func){
        if(!isset($this->_methods[$func])) {
            return null;
        }
        list(,$method,) = array_values((array)$this->_methods[$func]);
        return new LessMethod($this->_parent,$method);
    }

    /**
     * @param null $tag
     * @return array|mixed|null
     */
    public function getTag($tag = null){
        if($tag === null)
            return null;

        $tag = strtolower($tag);
        return isset($this->tags[$tag]) ? $this->tags[$tag] : [];
    }

    /**
     * @param null $tag
     * @return null|mixed
     */
    public function getFirstTag($tag = null){
        if($tag === null)
            return null;

        $tags = $this->getTag($tag);

        return (!$tags || empty($tags)) ? null : array_pop($tags);
    }

    /**
     * @param null $tag
     * @return null|mixed
     */
    public function getLastTag($tag = null){
        if($tag === null)
            return null;

        $tags = $this->getTag($tag);

        return (!$tags || empty($tags)) ? null : array_shift($tags);
    }

    private static $_varhelper = array('false' => false, 'true' => true, 'null' => null);


    /**
     * @param $command
     * @return array
     */
    public static function GetArrayVar($command) {
        if (is_array($command))
            return $command;

        $array = array();
        $ret = array();

        $reg = ':\[(.*?)\]:sx';
        $test = preg_match_all($reg, $command, $array, PREG_SET_ORDER);
        if (!$test)
            return $command;

        $t = explode("|", $array[0][1]);
        foreach ($t as $value) {
            if (!$value)
                continue;
            if (strpos($value, ":") === false)
                $ret[] = isset(static::$_varhelper[strtolower($value)]) ? static::$_varhelper[strtolower($value)] : $value;
            else{
                list($key, $v) = explode(":", trim($value));
                $ret[$key] = isset(static::$_varhelper[strtolower($v)]) ? static::$_varhelper[strtolower($v)] : $v;
            }
        }
        return $ret;
    }



    function __debugInfo()
    {
        return [
            'parent' => get_class($this->_parent),
            'tags'   => $this->tags
        ];
    }
}
/*
class LessPHP {
    const INIT = 0;
    const RENDER = 1;
    const FINISH = 2;


    function run($state = self::INIT) {
        if (isset($this->tags[$state])) {
            foreach ($this->tags[$state] as $value) {
                $tag = $value['tag'];
                $args = $value['args'];
                if (isset($this->_taghandler[$state][$tag])) {
                    $_func = $this->_taghandler[$state][$tag];
                    $this->_parent->$_func($args);
                }
            }
        }
    }

    function getTags() {
        return $this->tags;
    }


    /**
     * Creates the tag objects.
     *
     * @param string $tags Tag block to parse.
     *
     * @return void
     * /
    private static $cache;

    protected function parseTags($tags) {
        $sh = sha1($tags);

        if(!isset(self::$cache[$sh])){
            if(self::$cacheit == true)
                self::$cache[$sh] = SCache::getCache($sh);
        }

        if(isset(self::$cache[$sh]) && self::$cache[$sh]){
            $this->tags = self::$cache[$sh];
            return;
        }

        $_result = array();
        $result = array();
        $tags = trim($tags);
        if ('' !== $tags) {
            if ('@' !== $tags[0]) {
                throw new \LogicException('A tag block started with text instead of an actual tag,' . ' this makes the tag block invalid: ' . $tags);
            }
            foreach (explode("\n", $tags) as $tag_line) {
                if (trim($tag_line) === '') {
                    continue;
                }

                if (isset($tag_line[0]) && ($tag_line[0] === '@')) {
                    $_result[] = $tag_line;
                } else {
                    $_result[count($_result) - 1] .= PHP_EOL . $tag_line;
                }
            }

            //var_dump(implode('|', array_keys($this->_taghandler)));
            // create proper Tag objects
            foreach ($_result as $key => $tag_line) {
                $matches = null;

                if (!preg_match('/^@((.*)\s?)/us', trim($tag_line), $matches)) {
                    throw new \InvalidArgumentException('Invalid tag_line detected: ' . $tag_line);
                }
                if (!is_array($matches))
                    continue;
                list($tag, $args) = explode(' ', $matches[1], 2);
                $tag = strtolower($tag);
                $t = array('line' => $tag_line, 'tag' => $tag, 'args' => $args);
                if (isset($this->_taghandler[self::INIT][$tag]))
                    $result[self::INIT][$key] =  ($t);
                else if (isset($this->_taghandler[self::RENDER][$tag]))
                    $result[self::RENDER][$key] =  ($t);
                else if (isset($this->_taghandler[self::FINISH][$tag]))
                    $result[self::FINISH][$key] = ($t);
                else
                    unset($result[$key]);
            }
        }

        if(self::$cacheit == true)
            SCache::setCache($sh,$result);

        $this->tags = self::$cache[$sh] = $result;
    }

    static function callFunc($func){
        if(!is_string($func))
            return $func;
        $prefix = '/\$([a-zA-Z]{1,12}?)\((.+)\)/msi';
        //$prefix = '/\\$([0-9]*)?([a-z]?)([0-9]?)/msi';
        $text = preg_replace_callback(
            $prefix, array("LessPHP", '_func'), $func
        );
        return $text;
    }
    static function _func(&$matches) {
        list($all,$option, $args) = $matches;
        $args = explode(",",$args);

        switch ($option) {
            case 'call':
                $func = LessPHP::GetArrayVar(array_shift($args));
                return call_user_func_array($func,$args);
            case 'cond':
                if(LessPHP::callFunc($args[0]) == 1){
                    return trim($args[1]);
                }
                else {
                    return trim($args[2]);
                }
            default:
                var_dump($matches,$args);
        }
        return $matches[1];
    }

    private static $_varhelper = array('false' => false, 'true' => true, 'null' => null);

    static function GetArrayVar($command) {
        if (is_array($command))
            return $command;

        $array = array();
        $ret = array();

        $reg = ':\[(.*?)\]:sx';
        $test = preg_match_all($reg, $command, $array, PREG_SET_ORDER);

        if (!$test)
            return $command;

        $t = explode("|", $array[0][1]);
        foreach ($t as $value) {
            if (!$value)
                continue;
            if (strpos($value, ":") === false)
                $ret[] = isset(static::$_varhelper[strtolower($value)]) ? static::$_varhelper[strtolower($value)] : $value;
            else{
                list($key, $v) = explode(":", trim($value));
                $ret[$key] = isset(static::$_varhelper[strtolower($v)]) ? static::$_varhelper[strtolower($v)] : $v;
            }
        }
        return $ret;
    }

}*/
