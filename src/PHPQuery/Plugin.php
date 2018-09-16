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

namespace Bit\PHPQuery;

use Bit\Core\Traits\InstanceConfig;
use Bit\Traits\Statics;

/**
 * Class Plugin
 * @package Bit\PHPQuery
 */
class Plugin
{
    use Statics;
    use InstanceConfig;


    /**
     * Default config for this class
     *
     * @var array
     */
    protected $_defaultConfig = [
    ];


    /**
     * Plugin constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config($config);
    }

    /**
     * Plugin run
     * @param QueryObject $query
     * @param $args
     * @return QueryObject
     */
    public function invoke(QueryObject $query,$args){
        return $query;
    }
}