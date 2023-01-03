<?php
/**
 * BitCore-PHP:  Rapid Development Framework (https://phpcore.bitcoding.eu)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          https://phpcore.bitcoding.eu BitCore-PHP Project
 * @since         0.4.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Bit\LessPHP;

use Bit\LessPHP\Interfaces\Less as LessInterface;
use Bit\LessPHP\Traits\DocComment;
use Bit\Utility\Hash;

/**
 * Class Less
 * @package Bit\LessPHP
 */
abstract class Less
{
    use DocComment;

    /**
     * Cache this Model
     * @var bool
     */
    static $cacheit = true;

    /**
     * Refernce to Objecz
     * @var \Bit\LessPHP\Interfaces\Less|null
     */
    protected $_parent = null;

    /**
     * Cached Tags
     *
     * @var array|null
     */
    public $tags = [];

    /**
     * get Tags Array
     * @param null $tag
     * @return array|mixed|null
     */
    public function getTag($tag = null)
    {
        if ($tag === null)
            return null;

        $tag = strtolower($tag);
        return isset($this->tags[$tag]) ? $this->tags[$tag] : [];
    }

    /**
     * Get the First Tag
     * @param null $tag
     * @return null|mixed
     */
    public function getFirstTag($tag = null)
    {
        if ($tag === null)
            return null;

        $tags = $this->getTag($tag);

        return (!$tags || empty($tags)) ? null : array_pop($tags);
    }

    /**
     * Get Last Tag
     * @param null $tag
     * @return null|mixed
     */
    public function getLastTag($tag = null)
    {
        if ($tag === null)
            return null;

        $tags = $this->getTag($tag);

        return (!$tags || empty($tags)) ? null : array_shift($tags);
    }

    /**
     * var helper
     * @var array
     */
    private static $_varhelper = array('false' => false, 'true' => true, 'null' => null);


    /**
     * Parse arguments
     * @param $command
     * @return array
     */
    public static function GetArrayVar($command)
    {
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
            else {
                list($key, $v) = explode(":", trim($value));
                $ret[$key] = isset(static::$_varhelper[strtolower($v)]) ? static::$_varhelper[strtolower($v)] : $v;
            }
        }
        return $ret;
    }

    /**
     * DEBUG view
     * @return array
     */
    function __debugInfo()
    {
        return [
            'parent' => isset($this->_parent)
                ? get_class($this->_parent)
                : null,
            'tags' => $this->tags
        ];
    }
}
