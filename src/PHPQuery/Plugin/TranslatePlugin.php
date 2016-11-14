<?php
/**
 * Created by PhpStorm.
 * User: bitcoding
 * Date: 25.04.16
 * Time: 21:43
 */

namespace Bit\PHPQuery\Plugin;

use Bit\PHPQuery\Plugin as BasePlugin;
use Bit\PHPQuery\QueryObject;

class TranslatePlugin extends BasePlugin
{
    protected $_defaultConfig = [
        'selector'=>'data-speak',
        'OnTranslate' => null
    ];

    public function invoke(QueryObject $query,$args){
        
        
        
        var_dump([$this,$query,$args]);
        die();
        return $query;
    }
}