<?php
/**
 * BitCore-PHP:  Rapid Development Framework (https://phpcore.bitcoding.eu)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          https://phpcore.bitcoding.eu BitCore-PHP Project
 * @since         0.7.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Bit\Traits;

/**
 * Class Statics
 * @package Bit\Traits
 */
trait Statics
{
    /**
     * Instance Of Singleton
     * @var null
     */
    private static $instance = null;

    /**
     * Statics constructor.
     * @param null $config
     */
    public function __construct($config = null){}


    /**
     * return singleton instance
     * @param null|array $config
     * @return null|static|self
     */
    static function getStatic($config = null){
        if(!self::$instance)
            self::$instance = new static($config);
        return self::$instance;
    }
}