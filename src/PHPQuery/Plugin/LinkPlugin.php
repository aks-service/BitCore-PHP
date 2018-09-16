<?php
/**
 * BitCore-PHP:  Rapid Development Framework (https://phpcore.bitcoding.eu)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          https://phpcore.bitcoding.eu BitCore-PHP Project
 * @since         0.2.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Bit\PHPQuery\Plugin;

use Bit\LessPHP\Less;
use Bit\PHPQuery\Plugin as BasePlugin;
use Bit\PHPQuery\QueryObject;
use Bit\Routing\Router;

/**
 * Class LinkPlugin convert to router link
 * @package Bit\PHPQuery\Plugin
 */
class LinkPlugin extends BasePlugin
{
    /**
     * Config values
     * @var array
     */
    protected $_defaultConfig = [
        'selector'=>'a[href*="link_"],form[action*="link_"]',
        'explode' => 'link_',
        'OnGenerate' => null
    ];

    /**
     * Convert Link
     *
     * @param QueryObject $query
     * @param $_args
     * @return QueryObject
     */
    public function invoke(QueryObject $query,$_args){
        $func = $this->config('OnGenerate');

        $args = count($_args) ?  array_shift($_args) : [];
        $full = count($_args) ?  array_shift($_args) : [];

        $explode = $this->config('explode');

        $query->find($this->config('selector'))->each(function(QueryObject $node) use ($args,$explode,$full,$func){
            $tag = $node->nodes()[0]->tagName;
            $attr = null;
            switch($tag){
                case 'a':
                    $attr = "href";
                    break;
                case 'form':
                    $attr = "action";
                    break;
            }
            if(!$attr)
                return;

            $_link = explode($explode,$node->attr($attr));
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

            $node->attr($attr,$url);

            //$node->text($node->attr('href'));
        });
        //var_dump([$this,$query,$args]);
        //die();
        return $query;
    }
}