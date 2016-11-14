<?php
/**
 * Created by PhpStorm.
 * User: bitcoding
 * Date: 25.04.16
 * Time: 21:43
 */

namespace Bit\PHPQuery\Plugin;

use Bit\LessPHP\Less;
use Bit\PHPQuery\Plugin as BasePlugin;
use Bit\PHPQuery\QueryObject;
use Bit\Routing\Router;

class LinkPlugin extends BasePlugin
{
    protected $_defaultConfig = [
        'selector'=>'a[href*="link_"]',
        'explode' => 'link_',
        'OnGenerate' => null
    ];

    public function invoke(QueryObject $query,$_args){
        $func = $this->config('OnGenerate');

        $args = count($_args) ?  array_shift($_args) : [];
        $full = count($_args) ?  array_shift($_args) : [];

        $explode = $this->config('explode');

        $query->filter($this->config('selector'))->each(function(QueryObject $node) use ($args,$explode,$full,$func){
            $_link = explode($explode,$node->attr('href'));
            $href = array_pop($_link);

            $i = strpos($href, "[");
            if ($i !== false) {
                $args += Less::GetArrayVar(substr($href, $i));
                $href = substr($href, 0, $i);
            }

            list($href,$args) =$func ? $func($node,$href,$args) : [$href,$args];
            try {
                $url = Router::url(($href ? ['_name' => $href] : []) + $args, $full);
            } catch(\Exception $e){
                var_dump($node->html());

                $url = "not_found";
            }

            $node->attr('href',$url);

            //$node->text($node->attr('href'));
        });

        //var_dump([$this,$query,$args]);
        //die();
        return $query;
    }
}