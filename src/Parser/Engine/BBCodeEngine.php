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

namespace Bit\Parser\Engine;

use Bit\Parser\ParserEngine;
use Bit\Parser\Parser\Document;
use Bit\Parser\Parser\Node;
/**
 * Simple BBCoder Parser
 *
 * Class BBCodeEngine
 * @package Bit\Parser\Parser\Engine
 */
class BBCodeEngine extends ParserEngine
{
    /**
     * {@inheritDoc}
     * @param $text
     * @return Document
     */
    function parse($text){
        $document = new Document();
        $text = preg_replace( '([\x20\\t]*(?:\\r\\n|\\r|\\n))', "\n", $text );

        $line     = 1;
        $position = 1;


        $consume = "";

        $nodes[] =

        $level = 0;
        $oldType = $type = Node::TYPE_TEXT;
        $normal = true;
        $inline = $close = false;
        $forceTxt  = false;

        $currentNode = $document;
        for($i=0;$i<strlen($text);$i++,$position++){
            $t = $text[$i];

            $oldType = $type;
            switch($t){
                case '[':
                    if($type === Node::TYPE_TEXT){
                        $type = Node::TYPE_ELEMENT;
                    }
                    break;
                case ']':
                    if($type === Node::TYPE_ELEMENT){
                        $check = isset($text[$i+1]) ? ($text[$i+1] === ']') : false;
                        if($close || !$normal || $check) {
                            if($check){
                                $inline = true;
                                $i++; //skip;
                            }
                            $type = Node::TYPE_TEXT;
                        }
                        else
                        {
                            $type = Node::TYPE_TEXT;
                            if(preg_match('/["\']|\d|\$/' , $consume[0])){
                                $forceTxt = true;
                                $consume = '['.$consume.']';
                                break;
                            }
                        }
                    }
                    //$level++;
                    break;
                case '/':
                    if($type === Node::TYPE_ELEMENT){
                        if($text[$i-1] === '[')
                            $normal = true;
                        else if($text[$i-1] === ']')
                            $normal = false;
                        else{
                            $consume .= $t;
                            break;
                        }
                        $close = true;
                        break;
                    }
                default:
                    if(ord($t) === 10){
                        $line++;
                        $position = 1;
                    }
                    $consume .= $t;
            }

            if($oldType !=$type && $consume){
                if($oldType === Node::TYPE_TEXT || $forceTxt){
                    $txt = $document->createNewText($consume);
                    $currentNode->appendChild($txt);
                    $forceTxt = false;
                }
                elseif($oldType === Node::TYPE_ELEMENT){
                    if(!$close){
                        $element = $document->createNewElement($consume);
                        //var_dump([$currentNode]);
                        $element->inline = $inline;

                        $add =$currentNode->appendChild($element);
                        if(!$inline)
                            $currentNode = $add;

                        $inline = false;
                    }
                }

                if($close) {
                    $currentNode = $currentNode->parent;

                    $normal = true;
                    $close = false;
                }

                $consume = "";
            }
        }
        if($consume){
            $txt = $document->createNewText($consume);
            $currentNode->appendChild($txt);
        }
        /*echo '<pre>';
        print_r($document->childNodes);
        echo '</pre>';
        die();/**/
        return $document;
    }

    /**
     * {@inheritDoc}
     *
     * @param Node $node
     * @param null $list
     * @param bool $disable
     * @return string
     */
    function render(Node $node,&$list = null ,$disable = false){
        if(!$list)
            $list = (object) $this->config('Rules');

        $attrToString = function($key,$value){
            return (($key === 0) ? '': ' '.$key).(($value) ? '='.$value : '');
        };


        $nodes  = null;
        $nocode = false;

        switch($node->type){
            case Node::TYPE_TEXT:
                break;
            case Node::TYPE_ELEMENT:
                if(in_array($node->name,$this->config('disableForce')) && !$disable)
                    $nocode = $disable = true;
                if(in_array($node->name,$list->garbage) || isset($list->garbage[$node->name]))
                    break;
            case Node::TYPE_DOCUMENT:
                if(count($node->childNodes))
                    $nodes = $node->childNodes;
                break;
            default:
                var_dump($node->type);
        }

        $txt = "";

        if($nodes)
            foreach ($nodes as $_node){
                $t = $this->render($_node,$list,$disable);
                if($t !=="")
                    $txt .= $t;
            }

        switch($node->type){

            case Node::TYPE_ELEMENT:
                /*list($enable , $disable )*/
                $name = $node->name;
                //if(in_array($name,$list->garbage) || isset($list->garbage[$node->name]))
                $start = $end = "";
                //var_dump($list->render);
                if(in_array($name,$list->disable) || isset($list->disable[$name]) || $disable && !$nocode){
                    $attr = $node->attributes;
                    $attr = array_map($attrToString,array_keys($attr),array_values($attr));
                    $start = '['.$name.implode('',$attr).']';
                    $end = '[/'.$name.']';
                    //var_dump($node);
                    if($node->inline){
                        return '['.$start.']';
                    }
                }
                else if(isset($list->render[$name])){
                    $c = $list->render[$name];
                    if(isset($c['start']) && isset($c['end'])){
                        $start = $c['start'];
                        $end = $c['end'];
                    }
                    else if(isset($c['name'])){
                        $nn = $c['name'];
                        $start = '<'.$nn.'>';
                        $end = '</'.$nn.'>';
                    }
                    else if(isset($c['func'])){
                        $func = $c['func'];
                        return $func($node,$txt);
                    }
                }
                elseif(in_array($name,$list->render)){
                    $start = '<'.$name.'>';
                    $end = '</'.$name.'>';
                }
                elseif(!$nocode){
                    $attr = $node->attributes;
                    $attr = array_map($attrToString,array_keys($attr),array_values($attr));

                    $start = '['.$name.implode('',$attr).']';
                    $end = '[/'.$name.']';

                    if($node->inline){
                        return '['.$start.']';
                    }
                }

                return $start.$txt.$end;
                break;
            case Node::TYPE_TEXT:
                $func = $list->text;
                return $func($node);
            default:
                return $txt;
        }
        /*
        if($document)
        foreach ($document->chil){

        }

        var_dump($document);
        die();*/

    }
}