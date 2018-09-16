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
 * The following class generates VALID RFC 4211 COMPLIANT Universally Unique IDentifiers (UUID) version 3, 4 and 5.
 * 
 * Version 3 and 5 UUIDs are named based. They require a namespace (another valid UUID) and a value (the name). Given the same namespace and name, the output is always the same.
 * Version 4 UUIDs are pseudo-random.
 * 
 * UUIDs generated below validates using OSSP UUID Tool, and output for named-based UUIDs are exactly the same. This is a pure PHP implementation.
 * 
 *
 * @author      Andrew Moore <http://www.php.net/uniqid>
 */
class UUID {

    /**
     * A version 3 UUID is namespace generated.
     *
     * To determine the version 3 UUID corresponding to a given namespace
     * and name, the UUID of the namespace is transformed to a string
     * of bytes, concatenated with the input name, then hashed with MD5,
     * yielding 128 bits. Six or seven bits are then replaced by fixed
     * values, the 4-bit version (e.g. 0011 for version 3),
     * and the 2- or 3-bit UUID "variant" (e.g. 10 indicating a
     * RFC 4122 UUIDs, or 110 indicating a legacy Microsoft GUID).
     * ince 6 or 7 bits are thus predetermined, only 121 or 122 bits
     * contribute to the uniqueness of the UUID.
     *
     * @param $namespace
     * @param $name
     * @return bool|string
     */
    public static function v3($namespace, $name) {
        if (!self::is_valid($namespace))
            return false;

        // Get hexadecimal components of namespace
        $nhex = str_replace(array('-', '{', '}'), '', $namespace);

        // Binary Value
        $nstr = '';

        // Convert Namespace UUID to bits
        for ($i = 0; $i < strlen($nhex); $i+=2) {
            $nstr .= chr(hexdec($nhex[$i] . $nhex[$i + 1]));
        }

        // Calculate hash value
        $hash = md5($nstr . $name);

        return sprintf('%08s-%04s-%04x-%04x-%12s',
                // 32 bits for "time_low"
                substr($hash, 0, 8),
                // 16 bits for "time_mid"
                substr($hash, 8, 4),
                // 16 bits for "time_hi_and_version",
                // four most significant bits holds version number 3
                (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x3000,
                // 16 bits, 8 bits for "clk_seq_hi_res",
                // 8 bits for "clk_seq_low",
                // two most significant bits holds zero and one for variant DCE1.1
                (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
                // 48 bits for "node"
                substr($hash, 20, 12)
        );
    }

    /**
     * A version 4 UUID is randomly generated.
     *
     * As in other UUIDs, four bits are used to indicate version 4,
     * and 2 or 3 bits to indicate the variant (10 or 110 for variants 1
     * and 2, respectively). Thus, for variant 1 (that is, most UUIDs)
     * a random version 4 UUID will have 6 predetermined variant and
     * version bits, leaving 122 bits for the randomly-generated part,
     * for a total of 2122, or 5.3x1036 (5.3 undecillion) possible
     * version 4 variant 1 UUIDs. There are half as many possible
     * version 4 variant 2 UUIDs (legacy GUIDs) because there is one
     * less random bit available, 3 bits being consumed for the variant.
     *
     * @return string
     */
    public static function v4() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                // 32 bits for "time_low"
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                // 16 bits for "time_mid"
                mt_rand(0, 0xffff),
                // 16 bits for "time_hi_and_version",
                // four most significant bits holds version number 4
                mt_rand(0, 0x0fff) | 0x4000,
                // 16 bits, 8 bits for "clk_seq_hi_res",
                // 8 bits for "clk_seq_low",
                // two most significant bits holds zero and one for variant DCE1.1
                mt_rand(0, 0x3fff) | 0x8000,
                // 48 bits for "node"
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * A version 5 UUID is namespace generated.
     *
     * SHA1 is used instead of MD5.
     * Since SHA1 generates 160-bit digests,
     * the digest is truncated to 128-bits before
     * the version and variant bits are replaced.
     *
     * @param $namespace
     * @param $name
     * @return bool|string
     */
    public static function v5($namespace, $name) {
        if (!self::is_valid($namespace))
            return false;

        // Get hexadecimal components of namespace
        $nhex = str_replace(array('-', '{', '}'), '', $namespace);

        // Binary Value
        $nstr = '';

        // Convert Namespace UUID to bits
        for ($i = 0; $i < strlen($nhex); $i+=2) {
            $nstr .= chr(hexdec($nhex[$i] . $nhex[$i + 1]));
        }

        // Calculate hash value
        $hash = sha1($nstr . $name);

        return sprintf('%08s-%04s-%04x-%04x-%12s',
                // 32 bits for "time_low"
                substr($hash, 0, 8),
                // 16 bits for "time_mid"
                substr($hash, 8, 4),
                // 16 bits for "time_hi_and_version",
                // four most significant bits holds version number 5
                (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x5000,
                // 16 bits, 8 bits for "clk_seq_hi_res",
                // 8 bits for "clk_seq_low",
                // two most significant bits holds zero and one for variant DCE1.1
                (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
                // 48 bits for "node"
                substr($hash, 20, 12)
        );
    }

    /**
     * IS a Valid UUID
     *
     * @param $uuid
     * @return bool
     */
    public static function is_valid($uuid) {
        return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?' .
                        '[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1;
    }

}

?>