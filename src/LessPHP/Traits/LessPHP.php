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

namespace Bit\LessPHP\Traits;
use Bit\LessPHP\LessClass;

/**
 * Trait LessPHP
 * @package Bit\LessPHP\Traits
 */
trait LessPHP
{
    use Reflector;
    /**
     * Less Object Holder
     * @var LessClass|null
     */
    private $_less = null;

    /**
     * Return a Less Object
     *
     * @return LessClass|null
     */
    public function less(){
        if($this->_less === null)
            $this->_less = new LessClass($this);
        return $this->_less;
    }
}