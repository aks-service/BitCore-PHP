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
 * Class SCache
 * @package Bit\Helper
 */
class SCache
{

    /**
     * set
     * @param $label
     * @param $data
     */
    static function setCache($label, $data)
    {
        file_put_contents(CACHE . preg_replace('/[^0-9a-z\.\_\-]/i', '', strtolower($label)) . '.cache', '<?php $data =\'' . base64_encode(serialize($data)) . '\';');
    }

    /**
     * get
     * @param $label
     * @param bool $object
     * @param int $time
     * @return mixed|null
     */
    static function getCache($label, $object = false, $time = 0)
    {
        $filename = CACHE . preg_replace('/[^0-9a-z\.\_\-]/i', '', strtolower($label)) . '.cache';
        if (file_exists($filename) && ($time == 0 || ($time && (filemtime($filename) + $time >= time())))) {
            include $filename;
            return unserialize(base64_decode($data));
        }

        return null;
    }
}
