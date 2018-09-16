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

use Bit\Core\Bit;
use Bit\Enum\Lang;

/**
 * Simple Translate Trait
 * @package Bit\Traits
 */
trait Translate {
    /**
     * Cache messages
     * @var array
     */
    private static $_messageCache = array();

    /**
     * get lang message
     * @return mixed
     */
    abstract public function getFileMessage();  
    
    /**
     * return messages
     * @return string path to the error message file
     */
    private function getMessages() {
        $file = $this->getFileMessage();        
        $cE = Bit::getUUIDv5(md5($file));
        
        if (!isset(self::$_messageCache[$cE])){
            if (is_file($file))
                $file = file_get_contents ($file);   
            self::$_messageCache[$cE] = parse_ini_string($file, TRUE);
        }
        return self::$_messageCache[$cE];
    }
    
    /**
     * Translates an error code into an error message.
     * 
     * xxx: Todo default lang
     * @param string error code that is passed in the exception constructor.
     * @return string the translated error message
     */
    public function translateMessage($key) {
        $table = $this->getMessages();
        $lang = Bit::getPreferredLanguage();
        
        
        $msg  = (isset($table[$lang][$key])) ?
                    $table[$lang][$key]
                : 
                    ((isset($table[Lang::EN][$key])) ? $table[Lang::EN][$key] : null)
        ;
        $args = func_get_args();
        array_shift($args);
        return vsprintf($msg ? $msg : $key, array_shift($args));
    }

    /**
     * Get the passed in attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->_attributes;
    }
}