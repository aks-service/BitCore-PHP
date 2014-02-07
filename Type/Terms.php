<?php

class Terms {

    /**
     * @link http://www.php.net/microtime
     */
    static function getMicroTimeFloat() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }
    //
    public static function Diff($time,$init = -1) {
        $diff = date_diff(($init === -1  ? date_create() : date_create(date('Y-m-d H:i:s', $init))) , date_create(date('Y-m-d H:i:s', $time)) /*, $absolute*/);
        return $diff;
    }
}