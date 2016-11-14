<?php
/**
 * Created by PhpStorm.
 * User: bitcoding
 * Date: 25.05.16
 * Time: 22:06
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

}