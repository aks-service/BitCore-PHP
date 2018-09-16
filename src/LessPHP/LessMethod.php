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

namespace Bit\LessPHP;
use Bit\LessPHP\Interfaces\Less as LessInterface;
use Bit\Utility\Hash;

/**
 * Class LessMethod
 *
 * Provides a simple Method access
 *
 * @package Bit\LessPHP
 */
class LessMethod extends Less
{
    /**
     * Methods holder
     *
     * @var array|null
     */
    private $_method = null;

    /**
     * Method Params
     * TODO
     * @var array|null
     */
    private $param = null;

    /**
     * LessMethod constructor.
     * {@inheritDoc}
     *
     * @param LessClass $parent
     * @param \ReflectionMethod|null $method
     */
    function __construct(LessClass $parent,\ReflectionMethod $method = null)
    {
        //parent::__construct($parent);
        $this->_method = $method;
        $this->tags = Hash::merge($parent->tags, $method ? $this->parseDocBlock($method->getDocComment()) : []);
        /* TODO
        $params = array();
        foreach($method->getParameters() as $key=> &$param) {
            $params[$param->getName()] = (object)['param' => $param,
                'type'=> ($param->getClass() ? $param->getClass()->getName() : null),
                'passedbyRef'=>$param->isPassedByReference(),
                'allowNull'=>$param->allowsNull(),
                'pos' => $key
            ];
        }*/
    }

    /**
     * Return Debug Infos
     * @return array
     */
    function __debugInfo()
    {
        return parent::__debugInfo() + [
            'method'   => $this->_method
        ];
    }
}
