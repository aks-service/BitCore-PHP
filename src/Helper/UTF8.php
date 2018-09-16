<?php
/**
 * BitCore-PHP:  Rapid Development Framework (https://phpcore.bitcoding.eu)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          https://phpcore.bitcoding.eu BitCore-PHP Project
 * @since         0.1.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace Bit\Helper;
/**
 * Class UTF8
 * @package Bit\Helper
 */
class UTF8 {

    /**
     * return htmlchar
     * @link http://php.net/manual/de/function.chr.php
     *
     * @param $u
     * @return null|string|string[]
     */
    static function HtmlChar($u) {
        return mb_convert_encoding('&#' . intval($u) . ';', 'UTF-8', 'HTML-ENTITIES');
    }

}