<?php

class Matrix{

    public static function MergeRecursiveDistinct(array $array1, array &$array2, $depth = 0) {
        $merged = $array1;

        foreach ($array1 as $key => $value) {
            if (isset($array2[$key])) {
                if ($depth && is_array($array2[$key])) {
                    $merged [$key] = self::MergeRecursiveDistinct($value, $array2[$key], $depth - 1);
                } else {
                    $merged [$key] = $array2[$key];
                }
            }
        }

        return $merged;
    }
}

?>