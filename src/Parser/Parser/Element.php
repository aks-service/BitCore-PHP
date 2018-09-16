<?php
/**
 * BitCore-PHP:  Rapid Development Framework (https://phpcore.bitcoding.eu)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          https://phpcore.bitcoding.eu BitCore-PHP Project
 * @since         0.8.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Bit\Parser\Parser;


/**
 * Class Element
 * @package \Bit\Parser\Parser
 */
class Element extends Node
{
    /**
     * {@inheritDoc}
     */
    protected $type = self::TYPE_ELEMENT;

    /**
     * Element constructor.
     * @param $name
     * @param string $value
     */
    function __construct($name,$value = "")
    {
        $regex = '/(?:
         \w+=["\'][^"\'<>]+["\']
         |\w+=\w+
         |
         \w+
      )+/ix';


        $whitespace = strpos($name," ");
        $special = strpos($name,"=");

        $args = [];
        $arg = null;

        //$whitespace = strpos(" ",$name);
        if($special && (!$whitespace ||($whitespace && $whitespace > $special))){
            list($name,$arg) = explode('=',$name,2);
        }

        $cleanVal = function ($name){
            return preg_replace('/["\'](.*)["\']/i', "$1", $name);
        };

        $getArg = function($name,$needle = " ") use ($cleanVal){
            $pos = strpos($name,$needle);
            if($pos === false)
            {//var_dump($name);
                return [$name,""];
            }
            else{
                return [
                    substr($name,0,$pos),
                    substr($name,$pos+1)
                ];
            }
        };

        $whitespace = strpos($name," ");

        if($arg !== null || $whitespace){
            if($arg === null){
                list($name,$arg) = $getArg($name);
            }else{
                list($first,$arg) = $getArg($arg);
                $args[] = $cleanVal($first);
            }
            $result = [];
            preg_match_all(
                $regex,
                $arg,
                $result,
                PREG_PATTERN_ORDER);

            foreach ($result[0] as $arg){
                list($_name,$_arg) = $getArg($arg,"=");
                $args[$_name] = $cleanVal($_arg);
            }
        }

        $this->name = $name;
        $this->attributes = $args;
        parent::__construct($value);
    }

}