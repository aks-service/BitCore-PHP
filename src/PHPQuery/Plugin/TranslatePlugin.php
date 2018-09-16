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

use Bit\PHPQuery\Plugin as BasePlugin;
use Bit\PHPQuery\QueryObject;

/**
 * Class TranslatePlugin
 *
 * @package Bit\PHPQuery\Plugin
 */
class TranslatePlugin extends BasePlugin
{
    /**
     * {@inheritDoc}
     * @var array
     */
    protected $_defaultConfig = [
        'selector'=>'data-speak',
        'OnTranslate' => null
    ];

    /**
     * Translate elements
     *
     * TODO
     * @param QueryObject $query
     * @param $args
     * @return QueryObject
     */
    public function invoke(QueryObject $query,$args){
        
        
        
        var_dump([$this,$query,$args]);
        die();
        return $query;
    }
}