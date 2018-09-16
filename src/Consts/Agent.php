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

namespace Bit\Consts;
/**
 * Simple Agent Consts
 * @author      Bitcoding <bitcoding@bitcoding.eu>
 *
 * @since       0.1.0
 * @package     System/Const/Agent
 * @category    Const
 */
class Agent {
    const BOT = 0x00;
    const UNIX = 0x01;
    const ANDROID = 0x02;
    const MACOS = 0x04;
    const IOS = 0x08;
    const WINDOWS = 0x10;
    
    const UNKNOW    = 0x00;
    const LINUX     = 0x01;
    const APPLE     = 0x02;
    const MICROSOFT = 0x04;
    const SONY      = 0x08;
    const SAMSUNG   = 0x10;
    const NOKIA     = 0x20;
    const FACEBOOK  = 0x40;
    
    const LANG_EN   = 'en';
    const LANG_DE   = 'de';
    
    const X16 = 0x00;
    const X32 = 0x01;
    const X64 = 0x02;
    const X128 = 0x04; //Future ;)
}